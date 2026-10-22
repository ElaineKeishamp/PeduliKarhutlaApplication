<x-guest-layout>
    <h1 class="h4 text-center text-hutan mb-1">{{ __('Buat akun baru') }}</h1>
    <p class="text-secondary text-center mb-4">{{ __('Pilih peran Anda untuk mulai membantu.') }}</p>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3"><label class="form-label" for="name">{{ __('Nama lengkap') }}</label><input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="username">{{ __('Username') }}</label><input class="form-control @error('username') is-invalid @enderror" id="username" type="text" name="username" value="{{ old('username') }}" minlength="3" maxlength="30" required autocomplete="username"><div class="form-text">{{ __('Pisahkan username dari nama lengkap dan kata sandi.') }}</div>@error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="email">{{ __('Alamat email') }}</label><input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="phone">{{ __('Nomor telepon') }} <span class="text-secondary">({{ __('opsional') }})</span></label><input class="form-control @error('phone') is-invalid @enderror" id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="role">{{ __('Daftar sebagai') }}</label><select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required><option value="donatur" @selected(old('role', 'donatur') === 'donatur')>{{ __('Donatur') }}</option><option value="penggalang" @selected(old('role') === 'penggalang')>{{ __('Penggalang dana') }}</option></select>@error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="password">{{ __('Kata sandi') }}</label><input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required autocomplete="new-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-4"><label class="form-label" for="password_confirmation">{{ __('Ulangi kata sandi') }}</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div>
        <button class="btn btn-api w-100" type="submit">{{ __('Daftar sekarang') }}</button>
    </form>
    <p class="text-center small text-secondary mt-4 mb-0">{{ __('Sudah memiliki akun?') }} <a href="{{ route('login') }}" class="text-hutan fw-semibold">{{ __('Masuk') }}</a></p>
</x-guest-layout>
