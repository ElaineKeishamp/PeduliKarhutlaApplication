@extends('layouts.main')

@section('title', __('Tentang').' — Peduli Karhutla')

@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
    <div class="card p-4 p-lg-5">
        <span class="badge text-bg-success align-self-start mb-3">{{ __('Tentang aplikasi') }}</span>
        <h1 class="h2 text-hutan">Peduli Karhutla</h1>
        <p class="lead text-secondary">{{ __('Platform penggalangan bantuan untuk warga, relawan, dan satwa yang terdampak kebakaran hutan dan lahan.') }}</p>
        <hr>
        <h2 class="h5 text-hutan">{{ __('Cara kerja') }}</h2>
        <ol class="text-secondary"><li>{{ __('Penggalang membuat kampanye dengan target, tenggat, lokasi, dan bukti foto.') }}</li><li>{{ __('Admin meninjau dan menyetujui kampanye sebelum tampil kepada publik.') }}</li><li>{{ __('Donatur dapat memilih nominal dan mencoba alur pembayaran simulasi.') }}</li><li>{{ __('Donasi simulasi yang berhasil memperbarui progres dan menerbitkan sertifikat digital.') }}</li></ol>
        <div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle me-1"></i>{{ __('Aplikasi ini adalah prototipe lokal untuk pembelajaran. Pembayaran masih simulasi dan tidak memproses uang sungguhan.') }}</div>
    </div>
</div></div>
@endsection
