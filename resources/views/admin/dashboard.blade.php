@extends('layouts.main')

@section('title', __('Dashboard Admin').' — Peduli Karhutla')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <span class="badge text-bg-success mb-2">{{ __('Administrator') }}</span>
        <h1 class="h2 text-hutan mb-1">{{ __('Dashboard Admin') }}</h1>
        <p class="text-secondary mb-0">{{ __('Ringkasan aktivitas platform Peduli Karhutla.') }}</p>
    </div>
    <a class="btn btn-outline-success" href="/"><i class="bi bi-house me-1"></i>{{ __('Lihat halaman utama') }}</a>
</div>

<div class="row g-3">
    <div class="col-md-3"><div class="card p-4 h-100"><div class="text-secondary mb-2"><i class="bi bi-megaphone me-1"></i>{{ __('Total kampanye') }}</div><div class="display-6 fw-bold text-hutan">{{ $campaignCount }}</div></div></div>
    <div class="col-md-3"><div class="card p-4 h-100"><div class="text-secondary mb-2"><i class="bi bi-hourglass-split me-1"></i>{{ __('Menunggu ditinjau') }}</div><div class="display-6 fw-bold" style="color:var(--api)">{{ $pendingCampaignCount }}</div></div></div>
    <div class="col-md-3"><div class="card p-4 h-100"><div class="text-secondary mb-2"><i class="bi bi-heart me-1"></i>{{ __('Total donasi') }}</div><div class="display-6 fw-bold text-hutan">{{ $donationCount }}</div></div></div>
    <div class="col-md-3"><div class="card p-4 h-100"><div class="text-secondary mb-2"><i class="bi bi-receipt me-1"></i>{{ __('Laporan penyaluran menunggu') }}</div><div class="display-6 fw-bold text-hutan">{{ $pendingDisbursementCount }}</div></div></div>
</div>

<div class="card p-4 mt-4">
    <h2 class="h5 text-hutan">{{ __('Selamat datang, :name!', ['name' => auth()->user()->name]) }}</h2>
    <p class="text-secondary mb-0">{{ __('Tinjau pengajuan kampanye di bawah ini. Kampanye yang disetujui akan tampil di halaman Jelajahi Donasi.') }}</p>
</div>

<section class="mt-4">
    <h2 class="h4 text-hutan mb-3">{{ __('Pengajuan menunggu tinjauan') }}</h2>
    <div class="row g-3">
        @forelse ($pendingCampaigns as $campaign)
            <div class="col-12"><article class="card p-4">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div><h3 class="h5 mb-1">{{ $campaign->title }}</h3><div class="small text-secondary">{{ $campaign->location_name }} · {{ $campaign->category->name }} · diajukan {{ $campaign->user->name }}</div></div>
                    <span class="badge text-bg-warning align-self-start">{{ __('Menunggu') }}</span>
                </div>
                <p class="mt-3 mb-2">{{ $campaign->description }}</p>
                <div class="small text-secondary mb-3">{{ __('Target') }} Rp{{ number_format($campaign->target_amount, 0, ',', '.') }} · {{ __('Batas') }} {{ $campaign->deadline->format('d/m/Y') }} · {{ __(['manusia' => 'Bantuan warga', 'hewan' => 'Bantuan satwa', 'keduanya' => 'Bantuan warga & satwa'][$campaign->affected_type ?? 'manusia']) }} · {{ $campaign->affected_people }} {{ __('terdampak') }}</div>
                <form method="POST" action="{{ route('admin.campaigns.review', $campaign) }}" class="d-flex flex-wrap gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <input type="text" name="admin_note" class="form-control flex-grow-1" style="min-width:220px" placeholder="{{ __('Catatan admin (opsional)') }}">
                    <button class="btn btn-success" name="status" value="active" type="submit"><i class="bi bi-check-lg me-1"></i>{{ __('Setujui') }}</button>
                    <button class="btn btn-outline-danger" name="status" value="rejected" type="submit"><i class="bi bi-x-lg me-1"></i>{{ __('Tolak') }}</button>
                </form>
            </article></div>
        @empty
            <div class="col-12"><div class="card p-4 text-secondary">{{ __('Belum ada pengajuan kampanye baru.') }}</div></div>
        @endforelse
    </div>
</section>

<section class="mt-5">
    <h2 class="h4 text-hutan mb-2">{{ __('Pemeriksaan laporan penyaluran') }}</h2>
    <p class="text-secondary">{{ __('Periksa nominal, tujuan bantuan, dan foto bukti sebelum laporan ditampilkan kepada publik.') }}</p>
    <div class="row g-3">
        @forelse ($pendingDisbursements as $report)
            <div class="col-12"><article class="card p-4">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <span class="badge text-bg-warning mb-2">{{ __('Menunggu pemeriksaan') }}</span>
                        <h3 class="h5 mb-1">{{ $report->campaign->title }}</h3>
                        <div class="small text-secondary">{{ __('Penggalang') }}: {{ $report->campaign->user->name }} · {{ $report->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <strong class="fs-5 text-hutan">Rp{{ number_format($report->amount, 0, ',', '.') }}</strong>
                </div>
                <p class="mt-3 mb-2">{{ $report->purpose }}</p>
                @if ($report->proof_image)
                    <a class="d-inline-block mb-3" href="{{ route('disbursements.proof', $report) }}" target="_blank" rel="noopener">
                        <img src="{{ route('disbursements.proof', $report) }}" alt="{{ __('Bukti penyaluran') }}" class="rounded-3 border" style="max-height:240px;max-width:100%;object-fit:contain">
                    </a>
                @else
                    <div class="alert alert-danger">{{ __('Bukti foto belum tersedia.') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.disbursements.review', $report) }}" class="d-flex flex-wrap gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <input type="text" name="admin_note" class="form-control flex-grow-1" style="min-width:220px" placeholder="{{ __('Catatan pemeriksaan (opsional)') }}">
                    <button class="btn btn-success" name="status" value="approved" type="submit"><i class="bi bi-check-lg me-1"></i>{{ __('Setujui dan tampilkan') }}</button>
                    <button class="btn btn-outline-danger" name="status" value="rejected" type="submit"><i class="bi bi-x-lg me-1"></i>{{ __('Tolak laporan') }}</button>
                </form>
            </article></div>
        @empty
            <div class="col-12"><div class="card p-4 text-secondary">{{ __('Tidak ada laporan penyaluran yang menunggu pemeriksaan.') }}</div></div>
        @endforelse
    </div>
</section>

<section class="mt-5">
    <h2 class="h4 text-hutan mb-3">{{ __('Pesan, saran, dan keluhan terbaru') }}</h2>
    <div class="row g-3">
        @forelse ($feedbackMessages as $feedback)
            <div class="col-12"><article class="card p-4">
                <div class="d-flex flex-wrap justify-content-between gap-2"><div><span class="badge text-bg-light mb-2">{{ ucfirst($feedback->type) }}</span><h3 class="h5 mb-1">{{ $feedback->subject }}</h3><div class="small text-secondary">{{ $feedback->user->name }} · {{ $feedback->created_at->format('d/m/Y H:i') }}</div></div><span class="badge {{ $feedback->status === 'ditangani' ? 'text-bg-success' : 'text-bg-warning' }} align-self-start">{{ ucfirst($feedback->status) }}</span></div>
                <p class="mt-3 mb-3">{{ $feedback->message }}</p>
                <form method="POST" action="{{ route('admin.feedback.update', $feedback) }}" class="d-flex flex-wrap gap-2">
                    @csrf @method('PATCH')
                    <input type="text" name="admin_note" class="form-control flex-grow-1" style="min-width:220px" value="{{ $feedback->admin_note }}" placeholder="Catatan tindak lanjut admin">
                    <button class="btn {{ $feedback->status === 'ditangani' ? 'btn-outline-secondary' : 'btn-outline-success' }}" name="status" value="{{ $feedback->status === 'ditangani' ? 'baru' : 'ditangani' }}" type="submit">{{ $feedback->status === 'ditangani' ? __('Tandai belum selesai') : __('Tandai ditangani') }}</button>
                </form>
            </article></div>
        @empty
            <div class="col-12"><div class="card p-4 text-secondary">{{ __('Belum ada pesan dari pengguna.') }}</div></div>
        @endforelse
    </div>
</section>
@endsection
