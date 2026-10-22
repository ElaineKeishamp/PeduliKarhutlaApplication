@extends('layouts.main')

@section('title', 'Donasi Saya — Peduli Karhutla')

@section('content')
<div class="mb-4"><span class="badge text-bg-success mb-2">{{ __('Riwayat donasi') }}</span><h1 class="h2 text-hutan">{{ __('Donasi Saya') }}</h1><p class="text-secondary">{{ __('Lihat status pembayaran dan sertifikat donasimu.') }}</p></div>
<div class="card overflow-hidden">
    @forelse ($donations as $donation)
        <div class="p-4 border-bottom d-flex flex-wrap align-items-center gap-3 justify-content-between">
            <div><h2 class="h5 mb-1">{{ $donation->campaign->title }}</h2><div class="small text-secondary">{{ $donation->invoice_no }} · {{ $donation->created_at->format('d/m/Y H:i') }}</div></div>
            <div class="text-end"><strong class="d-block">Rp{{ number_format($donation->amount, 0, ',', '.') }}</strong><span class="badge {{ $donation->status === 'success' ? 'text-bg-success' : ($donation->status === 'pending' ? 'text-bg-warning' : 'text-bg-secondary') }}">{{ __(['success' => 'Berhasil', 'pending' => 'Menunggu pembayaran', 'failed' => 'Gagal'][$donation->status]) }}</span></div>
            <div class="d-flex gap-2">
                @if ($donation->status === 'pending')<a href="{{ route('payments.show', $donation->invoice_no) }}" class="btn btn-outline-success btn-sm">{{ __('Lanjut checkout') }}</a>@endif
                @if ($donation->certificate)<a href="{{ route('certificates.show', $donation->certificate->certificate_no) }}" class="btn btn-api btn-sm"><i class="bi bi-award me-1"></i>{{ __('Sertifikat') }}</a>@endif
            </div>
        </div>
    @empty
        <div class="p-5 text-center"><i class="bi bi-heart display-5" style="color:var(--api)"></i><h2 class="h5 mt-3">{{ __('Belum ada donasi') }}</h2><a class="btn btn-api mt-2" href="{{ route('campaigns.index') }}">{{ __('Jelajahi kampanye') }}</a></div>
    @endforelse
</div>
<div class="mt-4">{{ $donations->links() }}</div>
@endsection
