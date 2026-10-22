<form method="POST" action="{{ route('password.update') }}" class="mt-3">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12"><label class="form-label" for="current_password">Kata sandi saat ini</label><input class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" required>@error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="password">Kata sandi baru</label><input class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>@error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="password_confirmation">Ulangi kata sandi baru</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
    </div>
    <button class="btn btn-outline-success mt-3" type="submit">Perbarui kata sandi</button>
</form>
