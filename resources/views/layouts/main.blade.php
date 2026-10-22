<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name').' — Donasi Karhutla')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <span class="brand-mark d-inline-flex align-items-center justify-content-center rounded-circle"><i class="bi bi-fire"></i></span>
            Peduli Karhutla
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama" aria-controls="menuUtama" aria-expanded="false" aria-label="Buka menu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="menuUtama">
            <ul class="navbar-nav me-auto ms-lg-4 gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="/campaigns">{{ __('Jelajahi Donasi') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="/top-donatur">{{ __('Donatur Teratas') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">{{ __('Tentang') }}</a></li>
                @auth
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="/admin/dashboard">{{ __('Dashboard Admin') }}</a></li>
                    @elseif (auth()->user()->role === 'penggalang')
                        <li class="nav-item"><a class="nav-link" href="/fundraiser/dashboard">{{ __('Dashboard Penggalang') }}</a></li>
                    @elseif (auth()->user()->role === 'donatur')
                        <li class="nav-item"><a class="nav-link" href="{{ route('donations.mine') }}">{{ __('Donasi Saya') }}</a></li>
                    @endif
                @endauth
            </ul>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="language-switch" role="group" aria-label="Pilih bahasa">
                    <a href="{{ route('locale.switch', 'id') }}" class="{{ app()->getLocale() === 'id' ? 'active' : '' }}" lang="id" aria-current="{{ app()->getLocale() === 'id' ? 'true' : 'false' }}">ID</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}" lang="en" aria-current="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}">EN</a>
                </div>
                @auth
                    <a class="account-avatar-link" href="{{ route('profile.edit') }}" aria-label="Profil {{ auth()->user()->name }}" title="Profil">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/'.auth()->user()->profile_photo_path) }}" alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover">
                        @else
                            <i class="bi bi-person-circle fs-4"></i>
                        @endif
                    </a>
                    <a class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-2 px-3" href="{{ route('settings.index') }}"><i class="bi bi-gear" aria-hidden="true"></i><span>{{ __('Pengaturan') }}</span></a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-success btn-sm" type="submit">{{ __('Keluar') }}</button></form>
                @else
                    <a class="btn btn-link text-hutan text-decoration-none" href="{{ route('login') }}">{{ __('Masuk') }}</a>
                    <a class="btn btn-api btn-sm px-3" href="{{ route('register') }}">{{ __('Daftar') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
<main class="page-shell py-4 py-lg-5">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div>
        @endif
        @if ($errors->any() && ! request()->routeIs('fundraiser.dashboard'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ __('Mohon periksa kembali:') }}</strong>
                <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @yield('content')
    </div>
</main>
<footer class="py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small">
        <span><i class="bi bi-fire me-1"></i> Peduli Karhutla — {{ __('Bersama bantu warga dan satwa.') }}</span>
        <span><a class="link-light" href="{{ route('about') }}">{{ __('Tentang aplikasi') }}</a> · <a class="link-light" href="{{ auth()->check() ? route('feedback.create') : route('login') }}">{{ __('Hubungi / keluhan') }}</a></span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
