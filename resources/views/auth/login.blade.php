<x-guest-layout>
    <h1 class="h4 text-center text-hutan mb-1">{{ __('Selamat datang kembali') }}</h1>
    <p class="text-secondary text-center mb-4">{{ __('Masuk untuk melanjutkan aktivitas Anda.') }}</p>
    <x-auth-session-status class="mb-3" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3"><label class="form-label" for="email">{{ __('Alamat email') }}</label><input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="password">{{ __('Kata sandi') }}</label><input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required autocomplete="current-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="d-flex justify-content-between align-items-center mb-4"><label for="remember_me" class="form-check-label"><input id="remember_me" type="checkbox" class="form-check-input me-1" name="remember"> {{ __('Ingat saya') }}</label>@if (Route::has('password.request'))<a class="small text-hutan" href="{{ route('password.request') }}">{{ __('Lupa kata sandi?') }}</a>@endif</div>
        <button class="btn btn-api w-100" type="submit">{{ __('Masuk') }}</button>
    </form>
    <p class="text-center small text-secondary mt-4 mb-0">{{ __('Belum memiliki akun?') }} <a href="{{ route('register') }}" class="text-hutan fw-semibold">{{ __('Daftar') }}</a></p>
</x-guest-layout>
