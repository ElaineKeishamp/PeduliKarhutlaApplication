@extends('layouts.main')

@section('title', $campaign->title.' — Peduli Karhutla')

@section('content')
@php($midtransReady = app(\App\Services\MidtransPaymentService::class)->enabled())
<div class="mb-3"><a href="{{ route('campaigns.index') }}" class="text-hutan text-decoration-none"><i class="bi bi-arrow-left me-1"></i>{{ __('Kembali ke Jelajahi Donasi') }}</a></div>
<div class="row g-4 align-items-start">
    <div class="col-lg-7">
        <article class="card overflow-hidden">
            @if ($campaign->image)
                <img src="{{ asset('storage/'.$campaign->image) }}" alt="Foto {{ $campaign->title }}" class="w-100" style="max-height:380px;object-fit:cover">
            @else
                <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success" style="height:220px"><i class="bi bi-fire display-2"></i></div>
            @endif
            <div class="p-4 p-lg-5">
                <span class="badge text-bg-light mb-2">{{ $campaign->category->name }}</span>
                <h1 class="h2 text-hutan">{{ $campaign->title }}</h1>
                <p class="text-secondary"><i class="bi bi-geo-alt me-1"></i>{{ $campaign->location_name }} · {{ __('Diajukan oleh') }} {{ $campaign->user->name }}</p>
                <div class="d-flex flex-wrap gap-2 mb-4"><span class="badge text-bg-warning">{{ __('Level') }} {{ __(ucfirst($campaign->alert_level)) }}</span><span class="badge text-bg-light">{{ __(['manusia' => 'Warga terdampak', 'hewan' => 'Satwa/hewan terdampak', 'keduanya' => 'Warga dan satwa'][$campaign->affected_type ?? 'manusia']) }}</span>@if (($campaign->affected_type ?? 'manusia') !== 'hewan')<span class="badge text-bg-light">{{ $campaign->affected_people }} {{ __('warga terdampak') }}</span>@endif<span class="badge text-bg-light">{{ __('Batas') }} {{ $campaign->deadline->format('d M Y') }}</span></div>
                <h2 class="h5">{{ __('Kisah dan kebutuhan') }}</h2>
                <div class="text-secondary" style="white-space:pre-line">{{ $campaign->description }}</div>
            </div>
        </article>

        <section class="card p-4 mt-4">
            <h2 class="h5 text-hutan">{{ __('Donasi terbaru') }}</h2>
            @forelse ($recentDonations as $donation)
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <div><span class="fw-semibold">{{ $donation->is_anonymous ? __('Donatur anonim') : $donation->user->name }}</span>@if ($donation->message)<div class="small text-secondary">{{ $donation->message }}</div>@endif</div>
                    <strong>Rp{{ number_format($donation->amount, 0, ',', '.') }}</strong>
                </div>
            @empty
                <p class="text-secondary mb-0">{{ __('Jadilah donatur pertama untuk kampanye ini.') }}</p>
            @endforelse
        </section>

        <section class="card p-4 mt-4">
            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-start">
                <div>
                    <span class="badge text-bg-success mb-2"><i class="bi bi-shield-check me-1"></i>{{ __('Laporan ditinjau admin') }}</span>
                    <h2 class="h5 text-hutan mb-1">{{ __('Transparansi penyaluran') }}</h2>
                    <p class="small text-secondary mb-0">{{ __('Dana terkumpul dibandingkan dengan laporan penyaluran yang telah disetujui.') }}</p>
                </div>
            </div>
            <div class="row g-2 my-3">
                <div class="col-sm-4"><div class="bg-light rounded-3 p-3 h-100"><span class="small text-secondary">{{ __('Terkumpul') }}</span><strong class="d-block">Rp{{ number_format($campaign->collected_amount, 0, ',', '.') }}</strong></div></div>
                <div class="col-sm-4"><div class="bg-light rounded-3 p-3 h-100"><span class="small text-secondary">{{ __('Disalurkan menurut laporan disetujui') }}</span><strong class="d-block">Rp{{ number_format($campaign->total_disbursed, 0, ',', '.') }}</strong></div></div>
                <div class="col-sm-4"><div class="bg-light rounded-3 p-3 h-100"><span class="small text-secondary">{{ __('Belum tercatat dalam laporan disetujui') }}</span><strong class="d-block">Rp{{ number_format(max(0, (int) $campaign->collected_amount - $campaign->total_disbursed), 0, ',', '.') }}</strong></div></div>
            </div>
            @forelse ($campaign->disbursements as $report)
                <article class="border-top py-3">
                    <div class="d-flex justify-content-between gap-3"><strong>Rp{{ number_format($report->amount, 0, ',', '.') }}</strong><span class="small text-secondary">{{ $report->approved_at?->format('d M Y') }}</span></div>
                    <p class="mb-2 mt-1">{{ $report->purpose }}</p>
                    @if ($report->proof_image)
                        <a href="{{ asset('storage/'.$report->proof_image) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/'.$report->proof_image) }}" alt="{{ __('Bukti penyaluran bantuan') }}" class="img-fluid rounded-3 border" style="max-height:260px;object-fit:contain">
                            <span class="visually-hidden">{{ __('Buka bukti foto penyaluran') }}</span>
                        </a>
                    @endif
                </article>
            @empty
                <p class="border-top pt-3 text-secondary mb-0">{{ __('Belum ada laporan penyaluran yang disetujui admin.') }}</p>
            @endforelse
            <p class="small text-secondary border-top pt-3 mt-2 mb-0">{{ __('Laporan dan bukti foto dicatat oleh aplikasi. Aplikasi tidak memproses transfer dana ke rekening penerima.') }}</p>
        </section>

        <section class="card p-4 mt-4">
            <h2 class="h5 text-hutan">{{ __('Kabar dari penggalang') }}</h2>
            <p class="small text-secondary">{{ __('Perkembangan bantuan dilaporkan langsung oleh penggalang campaign.') }}</p>
            @forelse ($campaign->updates as $update)
                <article class="border-top py-3">
                    <div class="d-flex flex-wrap justify-content-between gap-2"><h3 class="h6 mb-0">{{ $update->title }}</h3><span class="small text-secondary">{{ $update->created_at->format('d M Y') }}</span></div>
                    <p class="mt-2 mb-2" style="white-space:pre-line">{{ $update->content }}</p>
                    @if ($update->amount_used)
                        <div class="small text-secondary mb-2">{{ __('Nominal penggunaan yang dicatat') }}: Rp{{ number_format($update->amount_used, 0, ',', '.') }}</div>
                    @endif
                    @if ($update->image)
                        <a href="{{ asset('storage/'.$update->image) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/'.$update->image) }}" alt="{{ __('Foto update campaign') }}" class="img-fluid rounded-3" style="max-height:320px;object-fit:cover"></a>
                    @endif
                </article>
            @empty
                <p class="border-top pt-3 text-secondary mb-0">{{ __('Penggalang belum mengirim kabar terbaru.') }}</p>
            @endforelse
        </section>
    </div>

    <aside class="col-lg-5">
        <div class="card p-4 sticky-top" style="top:90px" data-public-campaign-progress="{{ route('campaigns.progress', $campaign->slug) }}">
            <div class="d-flex justify-content-between"><span>{{ __('Terkumpul') }}</span><strong data-public-raised>Rp{{ number_format($campaign->collected_amount, 0, ',', '.') }}</strong></div>
            <div class="progress mt-2" role="progressbar" aria-label="Progres donasi" aria-valuenow="{{ $campaign->progress }}" aria-valuemin="0" aria-valuemax="100" data-public-progress><div class="progress-bar bg-success" style="width:{{ $campaign->progress }}%"></div></div>
            <div class="d-flex justify-content-between text-secondary small mt-2"><span>{{ __('dari') }} Rp{{ number_format($campaign->target_amount, 0, ',', '.') }}</span><span data-public-progress-text>{{ $campaign->progress }}%</span></div>
            <div class="small text-secondary mt-2"><span data-public-donations>{{ number_format($campaign->successful_donations_count ?? $campaign->donations()->where('status', 'success')->count()) }}</span> {{ __('donasi berhasil') }} · {{ __('pembaruan otomatis tiap 15 detik') }}</div>
            <hr>
            @if ($campaign->is_open)
                @auth
                    @if (auth()->user()->role === 'donatur')
                    <h2 class="h5 text-hutan">{{ __('Bantu kampanye ini') }}</h2>
                    @if ($midtransReady)
                        <div class="alert alert-info small"><i class="bi bi-shield-check me-1"></i>{{ __('Mode Midtrans Sandbox aktif. Pembayaran hanya untuk pengujian dan tidak memotong uang sungguhan.') }}</div>
                    @else
                        <div class="alert alert-warning small"><i class="bi bi-info-circle me-1"></i>{{ __('Mode simulasi lokal aktif. Pembayaran tidak terhubung ke Midtrans dan tidak memotong uang sungguhan.') }}</div>
                    @endif
                    <form method="POST" action="{{ route('campaigns.donate', $campaign->slug) }}">
                        @csrf
                        <div class="mb-3"><label class="form-label" for="amount">{{ __('Nominal donasi (rupiah)') }}</label><input class="form-control @error('amount') is-invalid @enderror" type="number" id="amount" name="amount" min="1000" max="1000000000" step="1000" value="{{ old('amount', 25000) }}" required>@error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="mb-3"><label class="form-label" for="payment_method">{{ $midtransReady ? __('Pilih kelompok metode pembayaran') : __('Metode simulasi') }}</label><select class="form-select" id="payment_method" name="payment_method" required>
                            @if ($midtransReady)
                                <option value="qris">QRIS</option><option value="transfer">{{ __('Transfer bank') }}</option><option value="ewallet">E-wallet</option>
                            @else
                                <option value="qris">{{ __('QRIS (simulasi)') }}</option><option value="transfer">{{ __('Transfer bank (simulasi)') }}</option><option value="ewallet">{{ __('E-wallet (simulasi)') }}</option>
                            @endif
                        </select></div>
                        <div class="mb-3"><label class="form-label" for="message">{{ __('Pesan untuk penggalang (opsional)') }}</label><textarea class="form-control" id="message" name="message" rows="2" maxlength="1000">{{ old('message') }}</textarea></div>
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_anonymous" value="1" id="is_anonymous"><label class="form-check-label" for="is_anonymous">{{ __('Lebih baik jangan tampilkan nama saya') }}</label></div>
                        <button type="submit" class="btn btn-api btn-lg w-100"><i class="bi bi-arrow-right-circle me-1"></i>{{ $midtransReady ? __('Lanjut ke pembayaran') : __('Lanjut ke checkout simulasi') }}</button>
                    </form>
                    @else
                        <p class="text-secondary mb-0">{{ __('Gunakan akun donatur untuk mencoba simulasi donasi.') }}</p>
                    @endif
                @else
                    <p class="text-secondary">{{ __('Masuk atau daftar sebagai donatur untuk mencoba simulasi donasi.') }}</p>
                    <a class="btn btn-api w-100" href="{{ route('login') }}">{{ __('Masuk untuk berdonasi') }}</a>
                @endauth
            @else
                <div class="alert alert-light mb-0">{{ __('Masa penggalangan campaign ini telah berakhir. Kabar dan laporan penyaluran tetap dapat dilihat di halaman ini.') }}</div>
            @endif
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const panel = document.querySelector('[data-public-campaign-progress]');
    if (!panel) return;
    const money = new Intl.NumberFormat('id-ID');
    const refresh = async () => {
        try {
            const response = await fetch(panel.dataset.publicCampaignProgress, {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok) return;
            const data = await response.json();
            panel.querySelector('[data-public-raised]').textContent = `Rp${money.format(data.collected_amount)}`;
            panel.querySelector('[data-public-progress-text]').textContent = `${data.progress}%`;
            panel.querySelector('[data-public-progress]').setAttribute('aria-valuenow', data.progress);
            panel.querySelector('.progress-bar').style.width = `${data.progress}%`;
            panel.querySelector('[data-public-donations]').textContent = money.format(data.successful_donation_count);
        } catch (_) {}
    };
    window.setInterval(refresh, 15000);
})();
</script>
@endpush
