@extends('layouts.main')

@section('title', __('Donatur Teratas').' — Peduli Karhutla')

@section('content')
<div class="mb-4">
    <span class="badge rounded-pill text-bg-warning-subtle text-warning-emphasis mb-2">{{ __('Terima kasih, #OrangBaik') }}</span>
    <h1 class="h2 text-hutan">{{ __('Donatur teratas') }}</h1>
    <p class="text-secondary">{{ __('Peringkat berdasarkan total donasi yang berhasil. Donasi anonim tidak ditampilkan.') }}</p>
</div>

<div class="card overflow-hidden">
    @forelse ($donors as $index => $donor)
        <div class="d-flex align-items-center gap-3 p-3 {{ $index < $donors->count() - 1 ? 'border-bottom' : '' }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success-subtle text-success fw-bold" style="width:42px;height:42px">{{ $index + 1 }}</span>
            <div class="flex-grow-1"><div class="fw-semibold">{{ $donor->name }}</div><div class="small text-secondary">{{ __('Donatur Peduli Karhutla') }}</div></div>
            <strong class="text-hutan">Rp{{ number_format($donor->total_amount, 0, ',', '.') }}</strong>
        </div>
    @empty
        <div class="p-5 text-center">
            <i class="bi bi-heart display-5" style="color:var(--api)"></i>
            <h2 class="h5 mt-3">{{ __('Belum ada donasi berhasil') }}</h2>
            <p class="text-secondary mb-0">{{ __('Nama donatur akan muncul di sini setelah donasi tercatat berhasil.') }}</p>
        </div>
    @endforelse
</div>
@endsection
