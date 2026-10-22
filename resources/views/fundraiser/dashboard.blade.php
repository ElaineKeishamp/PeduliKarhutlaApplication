@extends('layouts.main')

@section('title', __('Dashboard Penggalang').' — Peduli Karhutla')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>{{ __('Ada data yang perlu diperbaiki:') }}</strong>
        <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <span class="badge text-bg-success mb-2">{{ __('Ruang penggalang') }}</span>
        <h1 class="h2 text-hutan mb-1">{{ __('Dashboard Penggalang') }}</h1>
        <p class="text-secondary mb-0">{{ __('Pantau donasi, target, kabar campaign, dan laporan penyaluran dari satu tempat.') }}</p>
    </div>
    <a href="{{ route('campaigns.create') }}" class="btn btn-api"><i class="bi bi-plus-lg me-1"></i>{{ __('Ajukan campaign baru') }}</a>
</div>

<section aria-label="{{ __('Ringkasan akun penggalang') }}" class="row g-3 mb-4">
    <div class="col-6 col-xl-2"><article class="card p-3 h-100"><div class="small text-secondary">{{ __('Campaign saya') }}</div><div class="h3 text-hutan mb-0 mt-2" data-summary="campaign_count">{{ $campaignCount }}</div></article></div>
    <div class="col-6 col-xl-2"><article class="card p-3 h-100"><div class="small text-secondary">{{ __('Sedang aktif') }}</div><div class="h3 text-hutan mb-0 mt-2" data-summary="active_campaign_count">{{ $activeCampaignCount }}</div></article></div>
    <div class="col-6 col-xl-2"><article class="card p-3 h-100"><div class="small text-secondary">{{ __('Menunggu tinjauan') }}</div><div class="h3 text-hutan mb-0 mt-2" data-summary="pending_campaign_count">{{ $pendingCampaignCount }}</div></article></div>
    <div class="col-6 col-xl-2"><article class="card p-3 h-100"><div class="small text-secondary">{{ __('Donasi berhasil') }}</div><div class="h3 text-hutan mb-0 mt-2" data-summary="successful_donation_count">{{ number_format($successfulDonationCount) }}</div></article></div>
    <div class="col-6 col-xl-2"><article class="card p-3 h-100"><div class="small text-secondary">{{ __('Dana terkumpul') }}</div><div class="h5 text-hutan mb-0 mt-2" data-summary="total_raised">Rp{{ number_format($totalRaised, 0, ',', '.') }}</div></article></div>
    <div class="col-6 col-xl-2"><article class="card p-3 h-100"><div class="small text-secondary">{{ __('Penyaluran disetujui') }}</div><div class="h5 text-hutan mb-0 mt-2" data-summary="total_disbursed">Rp{{ number_format($totalDisbursed, 0, ',', '.') }}</div></article></div>
</section>

<div class="alert alert-info d-flex gap-2 align-items-start">
    <i class="bi bi-info-circle mt-1"></i>
    <div>{{ __('Dana di halaman ini adalah catatan donasi aplikasi. Laporan penyaluran perlu bukti dan persetujuan admin; aplikasi tidak melakukan transfer bank otomatis.') }}</div>
</div>

<section>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div><h2 class="h4 text-hutan mb-1">{{ __('Monitoring campaign') }}</h2><p class="text-secondary mb-0">{{ __('Buka rincian campaign untuk mengirim kabar dan mengelola laporan penyaluran.') }}</p><span class="small text-secondary">{{ __('Angka diperbarui otomatis tiap 15 detik.') }}</span></div>
    </div>

    @forelse ($campaigns as $campaign)
        @php
            $statusClass = ['active' => 'text-bg-success', 'pending' => 'text-bg-warning', 'rejected' => 'text-bg-danger', 'completed' => 'text-bg-secondary'][$campaign->status] ?? 'text-bg-light';
            $availableFunds = $campaign->remaining_funds;
        @endphp
        <article class="card p-3 p-lg-4 mb-3" id="campaign-{{ $campaign->id }}" data-fundraiser-campaign="{{ $campaign->id }}">
            <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge {{ $statusClass }}">{{ __(ucfirst($campaign->status)) }}</span>
                        @if ($campaign->category)<span class="badge text-bg-light">{{ $campaign->category->name }}</span>@endif
                        <span class="small text-secondary">{{ __('Batas') }} {{ $campaign->deadline->format('d M Y') }}</span>
                    </div>
                    <h3 class="h5 text-hutan mb-1">{{ $campaign->title }}</h3>
                    <div class="small text-secondary"><i class="bi bi-geo-alt me-1"></i>{{ $campaign->location_name }}</div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @if ($campaign->status === 'active')
                        <a class="btn btn-sm btn-outline-success" href="{{ route('campaigns.show', $campaign->slug) }}" target="_blank" rel="noopener">{{ __('Lihat halaman publik') }} <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                    @endif
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between small mb-1"><span>{{ __('Progress target') }}</span><strong data-campaign-progress-text>{{ $campaign->progress }}%</strong></div>
                    <div class="progress" role="progressbar" aria-label="{{ __('Progress target campaign') }}" aria-valuenow="{{ $campaign->progress }}" aria-valuemin="0" aria-valuemax="100" data-campaign-progress><div class="progress-bar bg-success" style="width:{{ $campaign->progress }}%"></div></div>
                    <div class="small text-secondary mt-1"><span data-campaign-raised>Rp{{ number_format($campaign->collected_amount, 0, ',', '.') }}</span> {{ __('terkumpul dari') }} Rp{{ number_format($campaign->target_amount, 0, ',', '.') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-secondary">{{ __('Sisa waktu') }}</div>
                    <strong>{{ max(0, $campaign->days_left) }} {{ __('hari') }}</strong>
                    <span class="text-secondary">· <span data-campaign-donations>{{ number_format($campaign->successful_donations_count) }}</span> {{ __('donasi berhasil') }}</span>
                </div>
            </div>

            @if ($campaign->admin_note)
                <div class="alert {{ $campaign->status === 'rejected' ? 'alert-danger' : 'alert-secondary' }} mt-3 mb-0"><strong>{{ __('Catatan admin') }}:</strong> {{ $campaign->admin_note }}</div>
            @endif

            <details class="mt-4">
                <summary class="fw-semibold text-hutan">{{ __('Buka monitoring dan laporan') }} ({{ $campaign->updates->count() }} {{ __('kabar') }} · {{ $campaign->disbursements->count() }} {{ __('laporan penyaluran') }})</summary>
                <div class="row g-4 pt-3">
                    <div class="col-12">
                        <div class="border rounded-4 p-3">
                            <div class="d-flex justify-content-between gap-2 align-items-center mb-2">
                                <h4 class="h6 text-hutan mb-0">{{ __('Donasi berhasil terbaru') }}</h4>
                                <span class="small text-secondary">{{ __('Daftar ini hanya menampilkan lima transaksi terakhir.') }}</span>
                            </div>
                            @forelse ($campaign->donations as $donation)
                                <div class="d-flex flex-wrap justify-content-between gap-2 border-top py-2">
                                    <div><strong>{{ $donation->is_anonymous ? __('Donatur anonim') : $donation->user->name }}</strong><div class="small text-secondary">{{ $donation->invoice_no }} · {{ $donation->paid_at?->format('d/m/Y H:i') }}</div></div>
                                    <strong>Rp{{ number_format($donation->amount, 0, ',', '.') }}</strong>
                                </div>
                            @empty
                                <p class="small text-secondary mb-0">{{ __('Belum ada transaksi donasi yang berhasil.') }}</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="border rounded-4 p-3 h-100">
                            <h4 class="h6 text-hutan">{{ __('Kabar penggunaan bantuan') }}</h4>
                            <p class="small text-secondary">{{ __('Ceritakan perkembangan di lapangan. Kabar ini langsung tampil di halaman campaign publik.') }}</p>
                            @if (in_array($campaign->status, ['active', 'completed'], true))
                                <form method="POST" action="{{ route('fundraiser.campaign-updates.store', $campaign) }}" enctype="multipart/form-data" class="mb-4">
                                    @csrf
                                    <div class="mb-2"><label class="form-label" for="update-title-{{ $campaign->id }}">{{ __('Judul kabar') }}</label><input class="form-control" id="update-title-{{ $campaign->id }}" name="title" maxlength="150" required></div>
                                    <div class="mb-2"><label class="form-label" for="update-content-{{ $campaign->id }}">{{ __('Cerita perkembangan dan penggunaan bantuan') }}</label><textarea class="form-control" id="update-content-{{ $campaign->id }}" name="content" rows="4" minlength="20" maxlength="5000" required></textarea></div>
                                    <div class="mb-2"><label class="form-label" for="update-amount-{{ $campaign->id }}">{{ __('Nominal penggunaan yang ingin dicatat (opsional)') }}</label><input class="form-control" type="number" min="0" max="1000000000" step="1000" id="update-amount-{{ $campaign->id }}" name="amount_used"><div class="form-text">{{ __('Catatan nominal pada kabar tidak menggantikan laporan penyaluran dengan bukti.') }}</div></div>
                                    <div class="mb-3"><label class="form-label" for="update-image-{{ $campaign->id }}">{{ __('Foto dokumentasi (opsional)') }}</label><input class="form-control" type="file" accept=".jpg,.jpeg,.png,.webp" id="update-image-{{ $campaign->id }}" name="image"></div>
                                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-send me-1"></i>{{ __('Publikasikan kabar') }}</button>
                                </form>
                            @else
                                <div class="alert alert-light small">{{ __('Kabar publik tersedia setelah campaign disetujui admin.') }}</div>
                            @endif

                            <h5 class="small text-uppercase text-secondary">{{ __('Riwayat kabar') }}</h5>
                            @forelse ($campaign->updates as $update)
                                <div class="border-top py-3">
                                    <div class="d-flex justify-content-between gap-2"><strong>{{ $update->title }}</strong><span class="small text-secondary">{{ $update->created_at->format('d/m/Y') }}</span></div>
                                    <p class="small mb-2 mt-1">{{ $update->content }}</p>
                                    @if ($update->amount_used)<div class="small text-secondary">{{ __('Penggunaan tercatat') }}: Rp{{ number_format($update->amount_used, 0, ',', '.') }}</div>@endif
                                    @if ($update->image)<a href="{{ asset('storage/'.$update->image) }}" target="_blank" rel="noopener"><img class="img-fluid rounded-3 mt-2" style="max-height:180px" src="{{ asset('storage/'.$update->image) }}" alt="{{ __('Foto dokumentasi') }}"></a>@endif
                                </div>
                            @empty
                                <p class="small text-secondary mb-0">{{ __('Belum ada kabar campaign.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="border rounded-4 p-3 h-100">
                            <h4 class="h6 text-hutan">{{ __('Laporan transparansi penyaluran') }}</h4>
                            <div class="row g-2 small mb-3">
                                <div class="col-4"><div class="bg-light rounded-3 p-2 h-100"><span class="text-secondary">{{ __('Terkumpul') }}</span><strong class="d-block">Rp{{ number_format($campaign->collected_amount, 0, ',', '.') }}</strong></div></div>
                                <div class="col-4"><div class="bg-light rounded-3 p-2 h-100"><span class="text-secondary">{{ __('Disetujui') }}</span><strong class="d-block" data-campaign-disbursed>Rp{{ number_format($campaign->total_disbursed, 0, ',', '.') }}</strong></div></div>
                                <div class="col-4"><div class="bg-light rounded-3 p-2 h-100"><span class="text-secondary">{{ __('Belum dilaporkan') }}</span><strong class="d-block" data-campaign-remaining>Rp{{ number_format($availableFunds, 0, ',', '.') }}</strong></div></div>
                            </div>
                            <p class="small text-secondary">{{ __('Catat penyaluran yang sudah dilakukan, sertakan foto bukti. Admin memeriksa laporan sebelum dipublikasikan kepada donatur.') }}</p>
                            @if (in_array($campaign->status, ['active', 'completed'], true) && $availableFunds >= 1000)
                                <form method="POST" action="{{ route('fundraiser.disbursements.store', $campaign) }}" enctype="multipart/form-data" class="mb-4">
                                    @csrf
                                    <div class="mb-2"><label class="form-label" for="disbursement-amount-{{ $campaign->id }}">{{ __('Nominal yang disalurkan') }}</label><input class="form-control" type="number" name="amount" id="disbursement-amount-{{ $campaign->id }}" min="1000" max="{{ $availableFunds }}" step="1000" required></div>
                                    <div class="mb-2"><label class="form-label" for="disbursement-purpose-{{ $campaign->id }}">{{ __('Keperluan dan penerima bantuan') }}</label><textarea class="form-control" name="purpose" id="disbursement-purpose-{{ $campaign->id }}" rows="3" minlength="10" maxlength="500" required></textarea></div>
                                    <div class="mb-3"><label class="form-label" for="disbursement-proof-{{ $campaign->id }}">{{ __('Foto bukti penyaluran') }}</label><input class="form-control" type="file" name="proof_image" id="disbursement-proof-{{ $campaign->id }}" accept=".jpg,.jpeg,.png,.webp" required></div>
                                    <button class="btn btn-api" type="submit"><i class="bi bi-file-earmark-check me-1"></i>{{ __('Kirim untuk diperiksa admin') }}</button>
                                </form>
                            @else
                                <div class="alert alert-light small">{{ $campaign->status === 'active' || $campaign->status === 'completed' ? __('Belum ada dana yang dapat dicatat sebagai penyaluran.') : __('Laporan penyaluran dapat dikirim setelah campaign disetujui.') }}</div>
                            @endif

                            <h5 class="small text-uppercase text-secondary">{{ __('Riwayat laporan') }}</h5>
                            @forelse ($campaign->disbursements as $report)
                                <div class="border-top py-3">
                                    <div class="d-flex justify-content-between gap-2"><strong>Rp{{ number_format($report->amount, 0, ',', '.') }}</strong><span class="badge {{ $report->status === 'approved' ? 'text-bg-success' : ($report->status === 'rejected' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ __('Status') }}: {{ __(ucfirst($report->status)) }}</span></div>
                                    <p class="small mb-1 mt-2">{{ $report->purpose }}</p>
                                    <div class="small text-secondary">{{ $report->created_at->format('d/m/Y') }}@if ($report->admin_note) · {{ __('Catatan admin') }}: {{ $report->admin_note }}@endif</div>
                                    @if ($report->proof_image)<a class="small" href="{{ route('disbursements.proof', $report) }}" target="_blank" rel="noopener">{{ __('Lihat bukti penyaluran') }} <i class="bi bi-box-arrow-up-right"></i></a>@endif
                                </div>
                            @empty
                                <p class="small text-secondary mb-0">{{ __('Belum ada laporan penyaluran.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </details>
        </article>
    @empty
        <div class="card p-5 text-center">
            <i class="bi bi-megaphone display-5 text-secondary"></i>
            <h2 class="h5 mt-3">{{ __('Belum ada campaign') }}</h2>
            <p class="text-secondary">{{ __('Ajukan campaign untuk mulai menggalang bantuan dan memantau perkembangannya di sini.') }}</p>
            <div><a href="{{ route('campaigns.create') }}" class="btn btn-api">{{ __('Buat campaign pertama') }}</a></div>
        </div>
    @endforelse
</section>
@endsection

@push('scripts')
<script>
(() => {
    const money = new Intl.NumberFormat('id-ID');
    const refresh = async () => {
        try {
            const response = await fetch(@json(route('fundraiser.dashboard.metrics')), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok) return;
            const data = await response.json();
            Object.entries(data.summary).forEach(([key, value]) => {
                const target = document.querySelector(`[data-summary="${key}"]`);
                if (target) target.textContent = key === 'total_raised' || key === 'total_disbursed' ? `Rp${money.format(value)}` : money.format(value);
            });
            Object.entries(data.campaigns).forEach(([id, campaign]) => {
                const card = document.querySelector(`[data-fundraiser-campaign="${id}"]`);
                if (!card) return;
                card.querySelector('[data-campaign-progress-text]').textContent = `${campaign.progress}%`;
                card.querySelector('[data-campaign-progress]').setAttribute('aria-valuenow', campaign.progress);
                card.querySelector('.progress-bar').style.width = `${campaign.progress}%`;
                card.querySelector('[data-campaign-raised]').textContent = `Rp${money.format(campaign.collected_amount)}`;
                card.querySelector('[data-campaign-donations]').textContent = money.format(campaign.successful_donations_count);
                const disbursed = card.querySelector('[data-campaign-disbursed]');
                const remaining = card.querySelector('[data-campaign-remaining]');
                if (disbursed) disbursed.textContent = `Rp${money.format(campaign.total_disbursed)}`;
                if (remaining) remaining.textContent = `Rp${money.format(campaign.remaining_funds)}`;
                const maxAmount = card.querySelector('[name="amount"]');
                if (maxAmount) maxAmount.max = campaign.remaining_funds;
            });
        } catch (_) {}
    };
    window.setInterval(refresh, 15000);
})();
</script>
@endpush
