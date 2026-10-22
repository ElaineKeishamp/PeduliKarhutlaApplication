@extends('layouts.main')

@section('title', __('Peduli Karhutla — Bantuan untuk Warga dan Satwa'))

@push('styles')
<style>
    .home-intro { padding-block: clamp(.25rem, 2vw, 1.25rem) clamp(2rem, 5vw, 4rem); }
    .home-hero { display: grid; grid-template-columns: 1.02fr .98fr; min-height: 540px; align-items:stretch; }
    .home-copy { position:relative; z-index:1; align-self:center; padding: clamp(1.6rem, 5vw, 4.2rem) clamp(1.6rem, 5vw, 4rem) clamp(1.6rem, 5vw, 4.2rem) 0; }
    .home-copy h1 { max-width: 650px; margin: 1rem 0 1.2rem; font-size: clamp(2.7rem, 5.2vw, 4.55rem); font-weight: 700; letter-spacing: -.065em; }
    .home-copy h1 em { color: var(--ember); font-style: normal; }
    .home-copy .lead { max-width: 530px; color: #58665f; font-size: 1.08rem; }
    .home-photo { position:relative; min-width:0; min-height:480px; overflow:hidden; border:1px solid rgba(24,63,50,.12); border-radius:1.5rem; background:#243c31; box-shadow:0 24px 55px rgba(24,63,50,.16); }
    .home-photo .carousel, .home-photo .carousel-inner, .home-photo .carousel-item { width:100%; height:100%; }
    .home-photo .carousel-item { min-height:480px; }
    .home-photo img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; }
    .home-photo .carousel-item::after { content:""; position:absolute; inset:32% 0 0; background:linear-gradient(180deg,transparent,rgba(9,26,20,.65)); }
    .photo-caption { position:absolute; z-index:2; right:1.35rem; bottom:1.35rem; left:1.35rem; padding:.95rem 1.1rem; border-radius:.8rem; color:#fff; }
    .photo-caption strong { display:block; margin-bottom:.2rem; font-size:1rem; }
    .photo-caption small { color:rgba(255,255,255,.82); }
    .photo-dots { position:absolute; z-index:4; top:1.2rem; right:1.2rem; display:flex; gap:.45rem; }
    .photo-dots [data-bs-target] { width:.48rem; height:.48rem; margin:0; border:0; border-radius:1rem; background:rgba(255,255,255,.7); opacity:1; transition:width .2s ease; }
    .photo-dots .active { width:1.5rem; background:#fff; }
    .photo-note { position:absolute; z-index:4; top:1.35rem; left:1.35rem; padding:.48rem .7rem; border-radius:999px; color:#fff; font-size:.71rem; }
    .home-proof { display:grid; grid-template-columns:repeat(3,1fr); gap:.8rem; margin-top:1.5rem; }
    .proof-item { min-height:90px; padding:1.05rem 1.2rem; border:1px solid rgba(24,63,50,.1); border-radius:1rem; background:rgba(255,255,255,.58); }
    .proof-item + .proof-item { border-left:1px solid rgba(24,63,50,.1); }
    .proof-item i { margin-right: .45rem; color: var(--ember); }
    .proof-item strong { display: block; color: var(--forest); font-size: .95rem; }
    .proof-item span { color: var(--muted); font-size: .84rem; }
    .home-pillars { padding-block: clamp(2rem, 6vw, 5rem); }
    .pillar { height:100%; padding:1.5rem; border:1px solid rgba(24,63,50,.1); border-radius:1.15rem 1.15rem 1.15rem 2.7rem; background:rgba(255,255,255,.66); transition:transform .2s ease, box-shadow .2s ease; }
    .pillar:hover { transform:translateY(-4px); box-shadow:0 16px 34px rgba(24,63,50,.09); }
    .pillar-icon { color:var(--ember); font-size:1.45rem; }
    .home-cta { display:flex; align-items:center; justify-content:space-between; gap:2rem; padding: clamp(1.5rem,4vw,3rem); border-radius:1.2rem 1.2rem 1.2rem 4rem; background:linear-gradient(115deg,#183f32,#245343); color:#fff; }
    .home-cta h2 { color:#fff; }
    .home-cta p { max-width:650px; color:#d0ddd5; }
    @media (max-width: 767.98px) {
        .home-intro { padding-top: .2rem; }
        .home-hero { grid-template-columns:1fr; gap:1.4rem; }
        .home-copy { padding:1.3rem .2rem 0; }
        .home-copy h1 { font-size: clamp(2.5rem, 12vw, 3.6rem); }
        .home-photo, .home-photo .carousel-item { min-height:340px; }
        .home-photo { border-radius:1.15rem; }
        .home-proof { grid-template-columns:1fr; gap:.6rem; margin-top:1rem; }
        .proof-item { min-height:0; padding:.9rem 1rem; }
        .proof-item + .proof-item { border-left:1px solid rgba(24,63,50,.1); }
        .home-cta { align-items:flex-start; flex-direction:column; }
    }
</style>
@endpush

@section('content')
<div class="home-intro">
    <section class="home-hero" aria-labelledby="home-title">
        <div class="home-copy">
            <span class="section-kicker"><i class="bi bi-activity me-1"></i>{{ __('Tanggap karhutla bersama') }}</span>
            <h1 id="home-title">{!! __('Bantuan nyata untuk <em>kehidupan</em> yang terdampak.') !!}</h1>
            <p class="lead">{{ __('Dukung warga, satwa, dan relawan di wilayah kebakaran hutan. Setiap kampanye ditinjau sebelum dibuka dan dapat dipantau perkembangannya.') }}</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn btn-api btn-lg" href="{{ route('campaigns.index') }}"><i class="bi bi-search me-2"></i>{{ __('Jelajahi kampanye') }}</a>
                <a class="btn btn-outline-forest btn-lg" href="{{ route('campaigns.create') }}">{{ __('Ajukan penggalangan') }} <i class="bi bi-arrow-up-right ms-1"></i></a>
            </div>
        </div>
        <div class="home-photo" aria-label="Foto tentang penanganan kebakaran dan satwa">
            <div id="homePhotoCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6500" data-bs-pause="hover">
                <div class="carousel-indicators photo-dots">
                    <button type="button" data-bs-target="#homePhotoCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Foto kebakaran hutan"></button>
                    <button type="button" data-bs-target="#homePhotoCarousel" data-bs-slide-to="1" aria-label="Petugas pemadam kebakaran"></button>
                    <button type="button" data-bs-target="#homePhotoCarousel" data-bs-slide-to="2" aria-label="Perawatan orangutan"></button>
                </div>
                <div class="photo-note glass-surface"><i class="bi bi-camera me-1"></i>{{ __('Foto dokumentasi nyata') }}</div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/9/92/Kebakaran_Hutan_dan_Lahan.jpg" alt="Kondisi kebakaran hutan dan lahan di Sukadana, Ketapang, Kalimantan Barat" fetchpriority="high">
                        <div class="photo-caption glass-surface"><strong>{{ __('Api mengubah kehidupan di sekitarnya') }}</strong><small>{{ __('Foto: Zahwa Huwaidah / Wikimedia Commons') }} · <a class="link-light" href="https://commons.wikimedia.org/wiki/File:Kebakaran_Hutan_dan_Lahan.jpg" target="_blank" rel="noopener">CC0 · {{ __('Sumber') }}</a></small></div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/4/40/Firefighter_pulling_hose_%2835252165236%29.jpg" alt="Petugas pemadam kebakaran menarik selang saat penanganan kebakaran hutan" loading="lazy">
                        <div class="photo-caption glass-surface"><strong>{{ __('Petugas bekerja menjaga area tetap aman') }}</strong><small>{{ __('Foto: Carol Underhill / USDA Forest Service') }} · <a class="link-light" href="https://commons.wikimedia.org/wiki/File:Firefighter_pulling_hose_(35252165236).jpg" target="_blank" rel="noopener">CC BY 2.0 · {{ __('Sumber') }}</a></small></div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/Sepilok_orang_utan_rehabilitation_centre.jpg" alt="Petugas pusat konservasi memberi makan orangutan dalam perawatan" loading="lazy">
                        <div class="photo-caption glass-surface"><strong>{{ __('Satwa juga perlu dirawat dan dipulihkan') }}</strong><small>{{ __('Foto: Kikigg76 / Wikimedia Commons') }} · <a class="link-light" href="https://commons.wikimedia.org/wiki/File:Sepilok_orang_utan_rehabilitation_centre.jpg" target="_blank" rel="noopener">CC BY 4.0 · {{ __('Sumber') }}</a></small></div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#homePhotoCarousel" data-bs-slide="prev" aria-label="Foto sebelumnya"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button>
                <button class="carousel-control-next" type="button" data-bs-target="#homePhotoCarousel" data-bs-slide="next" aria-label="Foto berikutnya"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>
            </div>
        </div>
    </section>
    <div class="home-proof" aria-label="Prinsip penyaluran bantuan">
        <div class="proof-item"><strong><i class="bi bi-patch-check"></i>{{ __('Ditinjau sebelum tayang') }}</strong><span>{{ __('Pengajuan kampanye diperiksa admin.') }}</span></div>
        <div class="proof-item"><strong><i class="bi bi-graph-up-arrow"></i>{{ __('Progres terlihat') }}</strong><span>{{ __('Target dan donasi terkumpul ditampilkan.') }}</span></div>
        <div class="proof-item"><strong><i class="bi bi-receipt"></i>{{ __('Laporan penyaluran') }}</strong><span>{{ __('Penggalang dapat menyampaikan kabar penggunaan dana.') }}</span></div>
    </div>
</div>

<section class="home-pillars">
    <div class="mb-4"><span class="section-kicker">{{ __('Satu kepedulian, banyak kebutuhan') }}</span><h2 class="section-heading mt-2">{{ __('Bantuan mengikuti keadaan di lapangan') }}</h2><p class="text-secondary mb-0">{{ __('Pilih kebutuhan yang paling dekat dengan dampak kebakaran.') }}</p></div>
    <div class="row g-4">
        <div class="col-md-4"><article class="pillar"><i class="bi bi-house-heart pillar-icon"></i><h3 class="h5 mt-3">{{ __('Warga dan relawan') }}</h3><p class="text-secondary mb-0">{{ __('Air bersih, masker, evakuasi, layanan kesehatan, dan kebutuhan logistik.') }}</p></article></div>
        <div class="col-md-4"><article class="pillar"><i class="bi bi-feather pillar-icon"></i><h3 class="h5 mt-3">{{ __('Satwa terdampak') }}</h3><p class="text-secondary mb-0">{{ __('Penyelamatan, pakan, perawatan, serta pemulihan habitat satwa.') }}</p></article></div>
        <div class="col-md-4"><article class="pillar"><i class="bi bi-journal-check pillar-icon"></i><h3 class="h5 mt-3">{{ __('Kabar penggunaan bantuan') }}</h3><p class="text-secondary mb-0">{{ __('Ikuti pembaruan dari penggalang dan laporan penyaluran kampanye.') }}</p></article></div>
    </div>
</section>

<section class="home-cta">
    <div><span class="section-kicker" style="color:#f3b18a">{{ __('Untuk penggerak di lapangan') }}</span><h2 class="h3 mt-2 mb-2">{{ __('Ada kebutuhan mendesak yang perlu dibantu?') }}</h2><p class="mb-0">{{ __('Ajukan kampanye dengan foto dan rincian kebutuhan. Kampanye akan ditinjau sebelum dibuka untuk donasi.') }}</p></div>
    <a class="btn btn-light btn-lg flex-shrink-0" href="{{ route('campaigns.create') }}">{{ __('Ajukan kampanye') }} <i class="bi bi-arrow-right ms-1"></i></a>
</section>
@endsection
