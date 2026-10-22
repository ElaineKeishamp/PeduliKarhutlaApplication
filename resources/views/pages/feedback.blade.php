@extends('layouts.main')

@section('title', __('Hubungi / keluhan').' — Peduli Karhutla')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
    <div class="mb-4"><a href="{{ route('settings.index') }}" class="small text-hutan text-decoration-none"><i class="bi bi-arrow-left me-1"></i>{{ __('Kembali ke Pengaturan') }}</a><h1 class="h2 text-hutan mt-2">{{ __('Hubungi kami / keluhan') }}</h1><p class="text-secondary">{{ __('Kirim pertanyaan, saran, atau keluhan. Pesanmu hanya terlihat olehmu dan admin.') }}</p></div>
    <form method="POST" action="{{ route('feedback.store') }}" class="card p-4 p-lg-5">
        @csrf
        <div class="mb-3"><label class="form-label" for="type">{{ __('Jenis pesan') }}</label><select class="form-select" id="type" name="type" required><option value="keluhan" @selected(old('type') === 'keluhan')>{{ __('Keluhan') }}</option><option value="saran" @selected(old('type') === 'saran')>{{ __('Saran') }}</option><option value="pertanyaan" @selected(old('type') === 'pertanyaan')>{{ __('Pertanyaan') }}</option></select></div>
        <div class="mb-3"><label class="form-label" for="subject">{{ __('Judul') }}</label><input class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" maxlength="150" required>@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="message">{{ __('Ceritakan detailnya') }}</label><textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" minlength="10" maxlength="5000" required>{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div><button class="btn btn-api" type="submit"><i class="bi bi-send me-1"></i>{{ __('Kirim pesan') }}</button></div>
    </form>

    <section class="mt-4"><h2 class="h5 text-hutan">{{ __('Pesan yang pernah dikirim') }}</h2><div class="card p-3">
        @forelse ($messages as $message)
            <div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}"><div class="d-flex justify-content-between gap-2"><strong>{{ $message->subject }}</strong><span class="badge {{ $message->status === 'ditangani' ? 'text-bg-success' : 'text-bg-warning' }}">{{ ucfirst($message->status) }}</span></div><div class="small text-secondary">{{ ucfirst($message->type) }} · {{ $message->created_at->format('d/m/Y H:i') }}</div><p class="mb-0 mt-1">{{ $message->message }}</p></div>
        @empty
            <p class="text-secondary mb-0">{{ __('Belum ada pesan yang dikirim.') }}</p>
        @endforelse
    </div></section>
</div></div>
@endsection
