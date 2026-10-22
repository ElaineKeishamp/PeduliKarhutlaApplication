@extends('layouts.main')

@section('title', __('Piagam apresiasi').' — Peduli Karhutla')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .certificate-wrap { max-width: 1080px; margin-inline: auto; }
    .certificate-sheet {
        --cert-green: #173d32;
        --cert-ink: #24342e;
        --cert-muted: #66736b;
        --cert-gold: #bd8950;
        position: relative;
        isolation: isolate;
        min-height: 640px;
        overflow: hidden;
        padding: clamp(2.25rem, 6vw, 5rem);
        border: 1px solid #d8d0c2;
        border-radius: 1rem;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(219, 230, 219, .52), transparent 34%),
            radial-gradient(ellipse at 0% 100%, rgba(239, 226, 204, .42), transparent 32%),
            #fffefa;
        color: var(--cert-ink);
        box-shadow: 0 22px 60px rgba(27, 47, 37, .1);
        text-align: center;
    }
    .certificate-sheet::before {
        position: absolute;
        z-index: -1;
        inset: 14px;
        border: 1px solid rgba(189, 137, 80, .55);
        border-radius: .55rem;
        content: "";
        pointer-events: none;
    }
    .certificate-sheet::after {
        position: absolute;
        z-index: -1;
        top: -90px;
        right: -78px;
        width: 240px;
        height: 240px;
        border: 1px solid rgba(23, 61, 50, .08);
        border-radius: 50%;
        box-shadow: 0 0 0 22px rgba(23, 61, 50, .025), 0 0 0 46px rgba(23, 61, 50, .02);
        content: "";
        pointer-events: none;
    }
    .certificate-brand { color: var(--cert-green); font-size: .76rem; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; }
    .certificate-emblem { display: inline-flex; width: 3.25rem; height: 3.25rem; align-items: center; justify-content: center; border: 1px solid rgba(189,137,80,.65); border-radius: 50%; color: var(--cert-green); font-size: 1.35rem; }
    .certificate-kicker { margin-top: 2rem; color: var(--cert-gold); font-size: .74rem; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; }
    .certificate-title { margin: 1.6rem 0 0; color: var(--cert-green); font-family: "Cormorant Garamond", Georgia, serif; font-size: clamp(2.2rem, 4vw, 3.25rem); font-weight: 700; letter-spacing: -.025em; line-height: 1; }
    .certificate-intro { margin: 1.7rem 0 .45rem; color: var(--cert-muted); font-size: 1rem; }
    .certificate-recipient { max-width: 100%; padding: 0 .5rem .55rem; border-bottom: 1px solid rgba(189,137,80,.65); color: var(--cert-green); font-family: "Cormorant Garamond", Georgia, serif; font-size: clamp(2.8rem, 6vw, 4.25rem); font-weight: 700; line-height: 1.02; overflow-wrap: anywhere; }
    .certificate-copy { max-width: 680px; margin: 1.35rem auto 0; color: var(--cert-muted); font-size: .98rem; line-height: 1.8; }
    .certificate-amount { display: inline-block; margin-top: 1.15rem; padding: .4rem 1.15rem; border: 1px solid rgba(23,61,50,.14); border-radius: 999px; background: rgba(231,238,232,.62); color: var(--cert-green); font-size: 1.2rem; font-weight: 700; letter-spacing: .01em; }
    .certificate-campaign { max-width: 710px; margin: 1.25rem auto 0; color: var(--cert-ink); font-size: 1.05rem; font-weight: 600; line-height: 1.5; }
    .certificate-campaign-label { display: block; margin-bottom: .25rem; color: var(--cert-muted); font-size: .7rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
    .certificate-meta { display: flex; justify-content: space-between; gap: 1rem; margin-top: 2.5rem; padding-top: 1rem; border-top: 1px solid rgba(23,61,50,.14); color: var(--cert-muted); font-size: .75rem; text-align: left; }
    .certificate-meta strong { color: var(--cert-ink); font-weight: 600; }
    .certificate-signoff { margin-top: 1.65rem; color: var(--cert-green); font-size: .82rem; font-weight: 600; }
    @media (max-width: 575.98px) {
        .certificate-sheet { min-height: 0; padding: 2.5rem 1.6rem; }
        .certificate-sheet::before { inset: 9px; }
        .certificate-meta { flex-direction: column; gap: .45rem; }
    }
    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        html, body { background: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        nav, footer, .no-print, .alert { display: none !important; }
        main.page-shell { min-height: 0 !important; padding: 0 !important; }
        main .container { max-width: none !important; padding: 0 !important; }
        .certificate-wrap { max-width: none; }
        .certificate-sheet { width: 100%; min-height: 188mm; padding: 18mm 24mm; border-radius: 0; box-shadow: none; break-inside: avoid; }
        .certificate-sheet::before { inset: 5mm; }
        .certificate-title { font-size: 32pt; }
        .certificate-recipient { font-size: 44pt; }
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">
    <div>
        <a href="{{ route('donations.mine') }}" class="text-hutan text-decoration-none"><i class="bi bi-arrow-left me-1"></i>{{ __('Kembali ke Donasi Saya') }}</a>
        <div class="text-secondary small mt-1">{{ __('Cetak atau simpan sebagai PDF untuk menyimpan piagam ini.') }}</div>
    </div>
    <button class="btn btn-api" onclick="window.print()"><i class="bi bi-download me-1"></i>{{ __('Cetak / Simpan sebagai PDF') }}</button>
</div>

<div class="certificate-wrap">
    <article class="certificate-sheet">
        <div class="certificate-emblem" aria-hidden="true"><i class="bi bi-fire"></i></div>
        <div class="certificate-brand mt-2">Peduli Karhutla</div>
        <h1 class="certificate-title">{{ __('Sertifikat Donasi') }}</h1>
        <p class="certificate-intro">{{ __('Piagam ini diberikan kepada') }}</p>
        <div class="certificate-recipient">{{ $certificate->donation->is_anonymous ? __('Donatur anonim') : $certificate->donation->user->name }}</div>

        <p class="certificate-copy">{{ __('Terima kasih telah ikut mendukung kampanye ini. Dukungan Anda berarti bagi upaya membantu warga dan satwa yang terdampak kebakaran hutan dan lahan.') }}</p>
        <div class="certificate-amount">Rp{{ number_format($certificate->donation->amount, 0, ',', '.') }}</div>
        <div class="certificate-campaign">
            <span class="certificate-campaign-label">{{ __('Kampanye yang didukung') }}</span>
            {{ $certificate->donation->campaign->title }}
        </div>

        <div class="certificate-signoff">{{ __('Dengan hormat,') }}<br>Peduli Karhutla</div>
        <div class="certificate-meta">
            <span>{{ __('Nomor piagam') }}: <strong>{{ $certificate->certificate_no }}</strong></span>
            <span>{{ __('Tanggal terbit') }}: <strong>{{ $certificate->issued_at->locale(app()->getLocale())->translatedFormat('d F Y') }}</strong></span>
        </div>
    </article>
</div>
@endsection
