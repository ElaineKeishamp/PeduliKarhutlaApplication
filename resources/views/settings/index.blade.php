@extends('layouts.main')

@section('title', 'Pengaturan — Peduli Karhutla')

@section('content')
<div class="mb-4"><span class="badge text-bg-success mb-2">{{ __('Akun') }}</span><h1 class="h2 text-hutan">{{ __('Pengaturan') }}</h1><p class="text-secondary">{{ __('Kelola profil dan temukan bantuan terkait aplikasi.') }}</p></div>
<div class="row g-3">
    <div class="col-md-6"><a href="{{ route('profile.edit') }}" class="card feature-card h-100 p-4 text-decoration-none text-reset"><i class="bi bi-person-gear fs-2 text-success mb-3"></i><h2 class="h5 text-hutan">{{ __('Profil & keamanan') }}</h2><p class="text-secondary mb-0">{{ __('Ubah nama, username, foto profil, kontak, dan kata sandi. Rekening pencairan tersedia untuk penggalang dana.') }}</p></a></div>
    <div class="col-md-6"><a href="{{ route('about') }}" class="card feature-card h-100 p-4 text-decoration-none text-reset"><i class="bi bi-info-circle fs-2 mb-3" style="color:var(--api)"></i><h2 class="h5 text-hutan">{{ __('Tentang aplikasi') }}</h2><p class="text-secondary mb-0">{{ __('Pelajari tujuan dan cara kerja Peduli Karhutla.') }}</p></a></div>
    <div class="col-md-6"><a href="{{ route('feedback.create') }}" class="card feature-card h-100 p-4 text-decoration-none text-reset"><i class="bi bi-chat-left-text fs-2 text-primary mb-3"></i><h2 class="h5 text-hutan">{{ __('Hubungi kami / keluhan') }}</h2><p class="text-secondary mb-0">{{ __('Kirim pertanyaan, saran, atau keluhan kepada admin aplikasi.') }}</p></a></div>
    @if (auth()->user()->role === 'donatur')<div class="col-md-6"><a href="{{ route('donations.mine') }}" class="card feature-card h-100 p-4 text-decoration-none text-reset"><i class="bi bi-heart fs-2 mb-3" style="color:var(--api)"></i><h2 class="h5 text-hutan">{{ __('Donasi saya') }}</h2><p class="text-secondary mb-0">{{ __('Lihat riwayat dan sertifikat donasi.') }}</p></a></div>@endif
</div>
@endsection
