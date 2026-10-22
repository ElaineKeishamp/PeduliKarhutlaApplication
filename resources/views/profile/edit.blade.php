@extends('layouts.main')

@section('title', 'Profil & Pengaturan Akun — Peduli Karhutla')

@section('content')
<div class="mb-4"><a href="{{ route('settings.index') }}" class="small text-hutan text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke Pengaturan</a><h1 class="h2 text-hutan mt-2">Profil & pengaturan akun</h1><p class="text-secondary">Atur nama, username, foto, kontak, dan rekening pencairanmu.</p></div>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card p-4 p-lg-5 mb-4">
    @csrf @method('PATCH')
    <h2 class="h5 text-hutan mb-4">Informasi profil</h2>
    <div class="d-flex align-items-center gap-3 mb-4">
        @if ($user->profile_photo_path)
            <img src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="Foto profil" class="rounded-circle border" style="width:76px;height:76px;object-fit:cover">
        @else
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success-subtle text-success fw-bold fs-3" style="width:76px;height:76px">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
        @endif
        <div class="flex-grow-1"><label class="form-label" for="profile_photo">Foto profil <span class="text-secondary">(opsional)</span></label><input class="form-control @error('profile_photo') is-invalid @enderror" type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/webp"><div class="form-text">JPG, PNG, WebP; maks. 2 MB.</div>@error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    </div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="name">Nama lengkap</label><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="username">Username</label><input class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" minlength="3" maxlength="30" required>@error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="phone">Nomor telepon</label><input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    </div>

    @if ($user->role === 'penggalang')
        <hr class="my-4">
        <h2 class="h5 text-hutan">Rekening pencairan</h2>
        <p class="text-secondary small">Opsional untuk menerima pencairan dana kampanye. Informasi rekening ini hanya ditampilkan di pengaturan akunmu.</p>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label" for="bank_name">Nama bank / e-wallet</label><input class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="Contoh: BCA, BRI, GoPay">@error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label" for="bank_account_holder">Nama pemilik rekening</label><input class="form-control @error('bank_account_holder') is-invalid @enderror" id="bank_account_holder" name="bank_account_holder" value="{{ old('bank_account_holder', $user->bank_account_holder) }}">@error('bank_account_holder')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label" for="bank_account_number">Nomor rekening / akun</label><input class="form-control @error('bank_account_number') is-invalid @enderror" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}" autocomplete="off">@error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div>
    @endif
    <div class="mt-4"><button class="btn btn-api" type="submit"><i class="bi bi-check2 me-1"></i>Simpan profil</button></div>
</form>

<div class="card p-4 p-lg-5">
    <h2 class="h5 text-hutan">Ubah kata sandi</h2>
    <p class="text-secondary small">Kata sandi tetap disimpan secara terpisah dari nama dan username.</p>
    @include('profile.partials.update-password-form')
</div>
@endsection
