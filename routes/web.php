<?php

use App\Http\Controllers\ProfileController;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Donation;
use App\Models\Disbursement;
use App\Models\FeedbackMessage;
use App\Services\MidtransPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/language/{locale}', function (string $locale, Request $request) {
    abort_unless(in_array($locale, ['id', 'en'], true), 404);
    $request->session()->put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect('/admin/dashboard'),
        'penggalang' => redirect('/fundraiser/dashboard'),
        default => redirect('/'),
    };
})->middleware('auth')->name('dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard', [
        'campaignCount' => Campaign::count(),
        'pendingCampaignCount' => Campaign::where('status', 'pending')->count(),
        'donationCount' => Donation::count(),
        'pendingCampaigns' => Campaign::with(['user', 'category'])->where('status', 'pending')->latest()->get(),
        'pendingDisbursements' => Disbursement::with(['campaign.user', 'campaign.category'])->where('status', 'pending')->latest()->get(),
        'pendingDisbursementCount' => Disbursement::where('status', 'pending')->count(),
        'feedbackMessages' => FeedbackMessage::with('user')->latest()->limit(10)->get(),
    ]);
})->middleware(['auth', 'role:admin'])->name('admin.dashboard');

Route::patch('/admin/campaigns/{campaign}/review', function (Request $request, Campaign $campaign) {
    $data = $request->validate([
        'status' => ['required', 'in:active,rejected'],
        'admin_note' => ['nullable', 'string', 'max:2000'],
    ]);

    abort_unless($campaign->status === 'pending', 409, 'Kampanye ini sudah ditinjau.');
    $campaign->update($data);

    return redirect()->route('admin.dashboard')->with('success', 'Status kampanye berhasil diperbarui.');
})->middleware(['auth', 'role:admin'])->name('admin.campaigns.review');

Route::patch('/admin/keluhan/{feedbackMessage}', function (Request $request, FeedbackMessage $feedbackMessage) {
    $data = $request->validate([
        'status' => ['required', 'in:baru,ditangani'],
        'admin_note' => ['nullable', 'string', 'max:2000'],
    ]);
    $feedbackMessage->update($data);

    return redirect()->route('admin.dashboard')->with('success', 'Status pesan pengguna berhasil diperbarui.');
})->middleware(['auth', 'role:admin'])->name('admin.feedback.update');

Route::patch('/admin/disbursements/{disbursement}/review', function (Request $request, Disbursement $disbursement) {
    $data = $request->validate([
        'status' => ['required', 'in:approved,rejected'],
        'admin_note' => ['nullable', 'required_if:status,rejected', 'string', 'max:2000'],
    ]);
    if ($data['status'] === 'approved' && (
        ! $disbursement->proof_image || ! Storage::disk('local')->exists($disbursement->proof_image)
    )) {
        throw ValidationException::withMessages(['status' => 'Laporan tidak dapat disetujui tanpa file foto bukti yang tersedia.']);
    }
    DB::transaction(function () use ($data, $disbursement, $request) {
        $lockedReport = Disbursement::whereKey($disbursement->id)->lockForUpdate()->firstOrFail();
        abort_unless($lockedReport->status === 'pending', 409, 'Laporan penyaluran ini sudah ditinjau.');
        if ($data['status'] === 'approved' && $lockedReport->proof_image && Storage::disk('local')->exists($lockedReport->proof_image)) {
            $copied = Storage::disk('public')->put($lockedReport->proof_image, Storage::disk('local')->get($lockedReport->proof_image));
            abort_unless($copied, 500, 'Bukti penyaluran gagal dipublikasikan.');
        } elseif ($data['status'] === 'approved') {
            throw ValidationException::withMessages(['status' => 'File bukti penyaluran tidak ditemukan.']);
        }
        $reviewDetails = $data['status'] === 'approved'
            ? ['approved_by' => $request->user()->id, 'approved_at' => now()]
            : ['approved_by' => null, 'approved_at' => null];
        $lockedReport->update($data + $reviewDetails);
    });
    if ($data['status'] === 'approved' && $disbursement->proof_image) {
        Storage::disk('local')->delete($disbursement->proof_image);
    }

    return redirect()->route('admin.dashboard')->with('success', 'Laporan penyaluran berhasil ditinjau.');
})->middleware(['auth', 'role:admin'])->name('admin.disbursements.review');

Route::get('/disbursements/{disbursement}/proof', function (Request $request, Disbursement $disbursement) {
    abort_unless(
        $request->user()->role === 'admin' || $disbursement->campaign()->where('user_id', $request->user()->id)->exists(),
        403,
    );
    abort_unless($disbursement->proof_image, 404);
    $disk = $disbursement->status === 'approved' ? Storage::disk('public') : Storage::disk('local');
    abort_unless($disk->exists($disbursement->proof_image), 404);

    return $disk->response($disbursement->proof_image);
})->middleware('auth')->name('disbursements.proof');

Route::get('/fundraiser/dashboard', function () {
    $campaigns = Campaign::with([
        'category',
        'donations' => fn ($query) => $query->where('status', 'success')->with('user')->latest()->limit(5),
        'updates' => fn ($query) => $query->latest(),
        'disbursements' => fn ($query) => $query->with('approver')->latest(),
    ])->withCount(['donations as successful_donations_count' => fn ($query) => $query->where('status', 'success')])
        ->where('user_id', auth()->id())->latest()->get();

    $campaignCount = $campaigns->count();
    $activeCampaignCount = $campaigns->where('status', 'active')->count();
    $pendingCampaignCount = $campaigns->where('status', 'pending')->count();
    $totalRaised = (int) $campaigns->sum('collected_amount');
    $totalDisbursed = (int) $campaigns->sum(fn ($campaign) => $campaign->total_disbursed);
    $successfulDonationCount = (int) $campaigns->sum('successful_donations_count');

    return view('fundraiser.dashboard', compact(
        'campaigns', 'campaignCount', 'activeCampaignCount', 'pendingCampaignCount',
        'totalRaised', 'totalDisbursed', 'successfulDonationCount',
    ));
})->middleware(['auth', 'role:penggalang'])->name('fundraiser.dashboard');

Route::get('/fundraiser/dashboard/metrics', function () {
    $campaigns = Campaign::where('user_id', auth()->id())
        ->withCount(['donations as successful_donations_count' => fn ($query) => $query->where('status', 'success')])
        ->latest()->get();
    $items = $campaigns->map(fn ($campaign) => [
        'id' => $campaign->id,
        'status' => $campaign->status,
        'collected_amount' => (int) $campaign->collected_amount,
        'progress' => $campaign->progress,
        'successful_donations_count' => (int) $campaign->successful_donations_count,
        'total_disbursed' => $campaign->total_disbursed,
        'remaining_funds' => $campaign->remaining_funds,
    ]);

    return response()->json([
        'summary' => [
            'campaign_count' => $campaigns->count(),
            'active_campaign_count' => $campaigns->where('status', 'active')->count(),
            'pending_campaign_count' => $campaigns->where('status', 'pending')->count(),
            'total_raised' => (int) $campaigns->sum('collected_amount'),
            'successful_donation_count' => (int) $campaigns->sum('successful_donations_count'),
            'total_disbursed' => (int) $items->sum('total_disbursed'),
        ],
        'campaigns' => $items->keyBy('id'),
        'updated_at' => now()->toIso8601String(),
    ])->header('Cache-Control', 'no-store, private');
})->middleware(['auth', 'role:penggalang'])->name('fundraiser.dashboard.metrics');

Route::post('/fundraiser/campaigns/{campaign}/updates', function (Request $request, Campaign $campaign) {
    abort_unless($campaign->user_id === $request->user()->id, 403);
    abort_unless(in_array($campaign->status, ['active', 'completed'], true), 409, 'Campaign harus disetujui sebelum laporan dipublikasikan.');
    $data = $request->validate([
        'title' => ['required', 'string', 'max:150'],
        'content' => ['required', 'string', 'min:20', 'max:5000'],
        'amount_used' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
        'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    ]);
    $imagePath = $request->file('image')?->store('campaign-updates', 'public');
    unset($data['image']);
    $campaign->updates()->create($data + ['image' => $imagePath]);

    return redirect()->route('fundraiser.dashboard')->with('success', 'Kabar penggunaan bantuan berhasil dipublikasikan pada halaman campaign.');
})->middleware(['auth', 'role:penggalang'])->name('fundraiser.campaign-updates.store');

Route::post('/fundraiser/campaigns/{campaign}/disbursements', function (Request $request, Campaign $campaign) {
    abort_unless($campaign->user_id === $request->user()->id, 403);
    abort_unless(in_array($campaign->status, ['active', 'completed'], true), 409, 'Campaign harus disetujui sebelum laporan penyaluran dibuat.');
    $data = $request->validate([
        'amount' => ['required', 'integer', 'min:1000', 'max:1000000000'],
        'purpose' => ['required', 'string', 'min:10', 'max:500'],
        'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    ]);

    DB::transaction(function () use ($request, $campaign, $data) {
        $lockedCampaign = Campaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
        abort_unless($lockedCampaign->user_id === $request->user()->id, 403);
        $reserved = (int) $lockedCampaign->disbursements()->whereIn('status', ['pending', 'approved'])->sum('amount');
        $available = max(0, (int) $lockedCampaign->collected_amount - $reserved);
        if ($data['amount'] > $available) {
            throw ValidationException::withMessages(['amount' => 'Nominal laporan melebihi dana yang belum dilaporkan.']);
        }

        $path = $request->file('proof_image')->store('disbursement-proofs', 'local');
        if (! $path) {
            throw ValidationException::withMessages(['proof_image' => 'Foto bukti gagal disimpan. Coba pilih file lain.']);
        }
        $lockedCampaign->disbursements()->create([
            'amount' => $data['amount'],
            'purpose' => $data['purpose'],
            'proof_image' => $path,
            'status' => 'pending',
        ]);
    });

    return redirect()->route('fundraiser.dashboard')->with('success', 'Laporan penyaluran dikirim ke admin untuk diperiksa. Laporan akan tampil publik setelah disetujui.');
})->middleware(['auth', 'role:penggalang'])->name('fundraiser.disbursements.store');

Route::get('/campaigns', function () {
    $campaigns = Campaign::with(['category', 'user'])
        ->where('status', 'active')
        ->whereDate('deadline', '>=', today())
        ->latest()
        ->paginate(9);

    return view('campaigns.index', compact('campaigns'));
})->name('campaigns.index');

Route::get('/campaigns/create', function () {
    if (! auth()->check()) {
        return redirect()->route('register')->with('message', 'Daftar sebagai penggalang dana untuk membuat kampanye.');
    }

    abort_unless(auth()->user()->role === 'penggalang', 403, 'Gunakan akun penggalang dana untuk membuat kampanye.');

    return view('campaigns.create', ['categories' => Category::orderBy('name')->get()]);
})->name('campaigns.create');

Route::get('/campaigns/{campaign:slug}', function (Campaign $campaign) {
    abort_unless(in_array($campaign->status, ['active', 'completed'], true), 404);
    $campaign->load([
        'category', 'user',
        'updates' => fn ($query) => $query->latest(),
        'disbursements' => fn ($query) => $query->where('status', 'approved')->with('approver')->latest(),
    ]);
    $recentDonations = $campaign->donations()->with('user')->where('status', 'success')->latest()->limit(10)->get();

    return view('campaigns.show', compact('campaign', 'recentDonations'));
})->name('campaigns.show');

Route::get('/campaigns/{campaign:slug}/progress', function (Campaign $campaign) {
    abort_unless(in_array($campaign->status, ['active', 'completed'], true), 404);
    $successfulDonationCount = $campaign->donations()->where('status', 'success')->count();

    return response()->json([
        'collected_amount' => (int) $campaign->collected_amount,
        'target_amount' => (int) $campaign->target_amount,
        'progress' => $campaign->progress,
        'successful_donation_count' => $successfulDonationCount,
        'updated_at' => now()->toIso8601String(),
    ])->header('Cache-Control', 'no-store');
})->name('campaigns.progress');

Route::post('/campaigns/{campaign:slug}/donate', function (Request $request, Campaign $campaign) {
    abort_unless($request->user()->role === 'donatur', 403, 'Masuk menggunakan akun donatur untuk memberi bantuan.');
    $data = $request->validate([
        'amount' => ['required', 'integer', 'min:1000', 'max:1000000000'],
        'payment_method' => ['required', 'in:transfer,qris,ewallet'],
        'message' => ['nullable', 'string', 'max:1000'],
        'is_anonymous' => ['nullable', 'boolean'],
    ]);

    abort_unless($campaign->status === 'active' && $campaign->deadline->isFuture(), 404);

    $midtrans = app(MidtransPaymentService::class);
    $gateway = $midtrans->enabled() ? 'midtrans' : 'simulation';

    $donation = DB::transaction(function () use ($request, $campaign, $data, $gateway) {
        $lockedCampaign = Campaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
        abort_unless($lockedCampaign->status === 'active' && $lockedCampaign->deadline->isFuture(), 404);

        return $lockedCampaign->donations()->create([
            'invoice_no' => ($gateway === 'midtrans' ? 'MID-' : 'SIM-').now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'user_id' => $request->user()->id,
            'amount' => $data['amount'],
            'message' => $data['message'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'payment_method' => $data['payment_method'],
            'payment_gateway' => $gateway,
            'payment_ref' => $gateway === 'simulation' ? 'SIM-'.Str::upper(Str::random(10)) : null,
            'status' => 'pending',
        ]);
    });

    if ($gateway === 'midtrans') {
        try {
            $donation->update(['snap_token' => $midtrans->createSnapToken($donation, $request)]);
        } catch (\Throwable $exception) {
            Log::warning('Midtrans Sandbox could not create a Snap token.', [
                'invoice_no' => $donation->invoice_no,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('payments.show', $donation->invoice_no)
                ->with('error', 'Midtrans belum berhasil dihubungi. Kamu bisa mencoba membuat sesi pembayaran lagi dari halaman tagihan.');
        }
    }

    return redirect()->route('payments.show', $donation->invoice_no);
})->middleware('auth')->name('campaigns.donate');

Route::get('/payments/{invoice_no}', function (string $invoice_no) {
    $donation = Donation::with(['campaign', 'user', 'certificate'])->where('invoice_no', $invoice_no)->firstOrFail();
    abort_unless($donation->user_id === auth()->id() || auth()->user()->role === 'admin', 403);

    return view('payments.show', compact('donation'));
})->middleware('auth')->name('payments.show');

Route::post('/payments/{invoice_no}/midtrans/token', function (Request $request, string $invoice_no, MidtransPaymentService $midtrans) {
    $donation = Donation::where('invoice_no', $invoice_no)->firstOrFail();
    abort_unless($donation->user_id === $request->user()->id || $request->user()->role === 'admin', 403);
    abort_unless($donation->payment_gateway === 'midtrans' && $donation->status === 'pending', 409);

    try {
        $donation->update(['snap_token' => $midtrans->createSnapToken($donation, $request)]);
    } catch (\Throwable $exception) {
        Log::warning('Midtrans Sandbox retry could not create a Snap token.', [
            'invoice_no' => $donation->invoice_no,
            'error' => $exception->getMessage(),
        ]);

        return redirect()->route('payments.show', $donation->invoice_no)
            ->with('error', 'Midtrans belum berhasil dihubungi. Periksa kembali Sandbox Key dan koneksi internet.');
    }

    return redirect()->route('payments.show', $donation->invoice_no)->with('success', 'Sesi pembayaran Sandbox berhasil dibuat.');
})->middleware('auth')->name('payments.midtrans.token');

Route::post('/payments/{invoice_no}/midtrans/refresh', function (Request $request, string $invoice_no, MidtransPaymentService $midtrans) {
    $donation = Donation::where('invoice_no', $invoice_no)->firstOrFail();
    abort_unless($donation->user_id === $request->user()->id || $request->user()->role === 'admin', 403);

    try {
        $donation = $midtrans->refreshStatus($donation);
    } catch (\Throwable $exception) {
        Log::warning('Midtrans Sandbox status refresh failed.', [
            'invoice_no' => $donation->invoice_no,
            'error' => $exception->getMessage(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Status belum dapat diperiksa. Coba lagi sebentar.'], 502);
        }

        return redirect()->route('payments.show', $donation->invoice_no)
            ->with('error', 'Status Midtrans belum dapat diperiksa. Coba lagi sebentar.');
    }

    if ($request->expectsJson()) {
        return response()->json(['status' => $donation->status]);
    }

    return redirect()->route('payments.show', $donation->invoice_no)
        ->with('success', $donation->status === 'success'
            ? 'Pembayaran berhasil diverifikasi oleh Midtrans.'
            : 'Status pembayaran saat ini: '.str($donation->status)->headline().'.');
})->middleware('auth')->name('payments.midtrans.refresh');

Route::post('/payments/midtrans/notification', function (Request $request, MidtransPaymentService $midtrans) {
    abort_unless($midtrans->enabled(), 503);

    try {
        $midtrans->handleNotification($request->all());
    } catch (\InvalidArgumentException $exception) {
        Log::notice('Midtrans notification signature was rejected.', [
            'order_id' => $request->input('order_id'),
        ]);

        return response('Invalid notification signature', 403);
    } catch (\Throwable $exception) {
        Log::warning('Midtrans notification was rejected or could not be processed.', [
            'order_id' => $request->input('order_id'),
            'error' => $exception->getMessage(),
        ]);

        return response('Notification processing failed', 500);
    }

    return response('OK', 200);
})->name('payments.midtrans.notification');

Route::post('/payments/{invoice_no}/simulate', function (Request $request, string $invoice_no) {
    $donation = DB::transaction(function () use ($request, $invoice_no) {
        $donation = Donation::where('invoice_no', $invoice_no)->lockForUpdate()->firstOrFail();
        abort_unless($donation->user_id === $request->user()->id || $request->user()->role === 'admin', 403);
        abort_unless($donation->payment_gateway === 'simulation', 409, 'Gunakan halaman pembayaran Midtrans untuk transaksi ini.');

        if ($donation->status === 'pending') {
            $campaign = Campaign::whereKey($donation->campaign_id)->lockForUpdate()->firstOrFail();
            $donation->update(['status' => 'success', 'paid_at' => now()]);
            $campaign->increment('collected_amount', $donation->amount);
            Certificate::firstOrCreate(
                ['donation_id' => $donation->id],
                [
                    'certificate_no' => 'PK-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                    'issued_at' => now(),
                ],
            );
        }

        return $donation->fresh(['campaign', 'user', 'certificate']);
    });

    return redirect()->route('payments.show', $donation->invoice_no)->with('success', 'Pembayaran simulasi berhasil. Tidak ada uang sungguhan yang ditagihkan.');
})->middleware('auth')->name('payments.simulate');

Route::get('/donasi-saya', function () {
    abort_unless(auth()->user()->role === 'donatur', 403);
    Donation::where('user_id', auth()->id())->where('status', 'success')->doesntHave('certificate')->get()->each(function (Donation $donation) {
        Certificate::firstOrCreate(
            ['donation_id' => $donation->id],
            [
                'certificate_no' => 'PK-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'issued_at' => now(),
            ],
        );
    });
    $donations = Donation::with(['campaign', 'certificate'])->where('user_id', auth()->id())->latest()->paginate(10);

    return view('donors.donations', compact('donations'));
})->middleware('auth')->name('donations.mine');

Route::get('/sertifikat/{certificate_no}', function (string $certificate_no) {
    $certificate = Certificate::with(['donation.campaign', 'donation.user'])
        ->where('certificate_no', $certificate_no)
        ->firstOrFail();
    abort_unless($certificate->donation->user_id === auth()->id() || auth()->user()->role === 'admin', 403);

    return view('certificates.show', compact('certificate'));
})->middleware('auth')->name('certificates.show');

Route::get('/top-donatur', function () {
    $donors = Donation::query()
        ->join('users', 'users.id', '=', 'donations.user_id')
        ->where('donations.status', 'success')
        ->where('donations.is_anonymous', false)
        ->selectRaw('users.id, users.name, SUM(donations.amount) as total_amount')
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total_amount')
        ->limit(20)
        ->get();

    return view('donors.index', compact('donors'));
})->name('donors.index');

Route::post('/campaigns', function (Request $request) {
    $data = $request->validate([
        'category_id' => ['required', 'exists:categories,id'],
        'title' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'min:30'],
        'location_name' => ['required', 'string', 'max:255'],
        'latitude' => ['required', 'numeric', 'between:-90,90'],
        'longitude' => ['required', 'numeric', 'between:-180,180'],
        'alert_level' => ['required', 'in:waspada,siaga,darurat'],
        'affected_people' => ['required', 'integer', 'min:0'],
        'affected_type' => ['required', 'in:manusia,hewan,keduanya'],
        'target_amount' => ['required', 'integer', 'min:10000'],
        'deadline' => ['required', 'date', 'after:today'],
        'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    ]);

    $baseSlug = Str::slug($data['title']);
    $slug = $baseSlug;
    for ($suffix = 2; Campaign::where('slug', $slug)->exists(); $suffix++) {
        $slug = $baseSlug.'-'.$suffix;
    }

    unset($data['image']);
    $imagePath = $request->file('image')?->store('campaign-posters', 'public');

    Campaign::create($data + [
        'user_id' => $request->user()->id,
        'slug' => $slug,
        'status' => 'pending',
        'image' => $imagePath,
    ]);

    return redirect()->route('fundraiser.dashboard')->with('success', 'Kampanye berhasil diajukan dan menunggu persetujuan admin.');
})->middleware(['auth', 'role:penggalang'])->name('campaigns.store');

Route::get('/pengaturan', fn () => view('settings.index'))->middleware('auth')->name('settings.index');
Route::get('/tentang', fn () => view('pages.about'))->name('about');
Route::get('/keluhan', function () {
    $messages = FeedbackMessage::where('user_id', auth()->id())->latest()->limit(10)->get();

    return view('pages.feedback', compact('messages'));
})->middleware('auth')->name('feedback.create');
Route::post('/keluhan', function (Request $request) {
    $data = $request->validate([
        'type' => ['required', 'in:keluhan,saran,pertanyaan'],
        'subject' => ['required', 'string', 'max:150'],
        'message' => ['required', 'string', 'min:10', 'max:5000'],
    ]);

    FeedbackMessage::create($data + ['user_id' => $request->user()->id]);

    return redirect()->route('feedback.create')->with('success', 'Pesanmu sudah dikirim dan tercatat. Admin akan meninjaunya.');
})->middleware('auth')->name('feedback.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
