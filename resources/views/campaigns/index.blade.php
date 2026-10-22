@extends('layouts.main')

@section('title', __('Jelajahi Donasi').' — Peduli Karhutla')

@section('content')
<header class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
        <span class="section-kicker">{{ __('Pilih bantuan yang ingin kamu dukung') }}</span>
        <h1 class="section-heading mt-2 mb-2">{{ __('Jelajahi kampanye') }}</h1>
        <p class="text-secondary mb-0">{{ __('Kampanye di halaman ini telah disetujui admin dan masih menerima donasi.') }}</p>
    </div>
    @auth
        @if (auth()->user()->role === 'penggalang')
            <a class="btn btn-outline-forest" href="{{ route('campaigns.create') }}"><i class="bi bi-plus-lg me-1"></i>{{ __('Ajukan kampanye') }}</a>
        @endif
    @else
        <a class="btn btn-outline-forest" href="{{ route('register') }}">{{ __('Ajukan penggalangan') }} <i class="bi bi-arrow-up-right ms-1"></i></a>
    @endauth
</header>

<div class="row g-4">
    @forelse ($campaigns as $campaign)
        <div class="col-md-6 col-lg-4">
            <article class="card campaign-card h-100">
                @if ($campaign->image)
                    <img src="{{ asset('storage/'.$campaign->image) }}" class="card-img-top" alt="Foto {{ $campaign->title }}" loading="lazy">
                @else
                    <div class="campaign-placeholder d-flex align-items-center justify-content-center"><i class="bi bi-fire display-4"></i></div>
                @endif
                <div class="card-body d-flex flex-column">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge rounded-pill text-bg-light">{{ $campaign->category->name }}</span>
                        <span class="badge rounded-pill text-bg-success-subtle text-success-emphasis">{{ __(['manusia' => 'Warga terdampak', 'hewan' => 'Satwa terdampak', 'keduanya' => 'Warga & satwa'][$campaign->affected_type ?? 'manusia']) }}</span>
                    </div>
                    <h2 class="h5 mb-2"><a class="text-hutan text-decoration-none" href="{{ route('campaigns.show', $campaign->slug) }}">{{ $campaign->title }}</a></h2>
                    <p class="text-secondary small mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $campaign->location_name }}</p>
                    <p class="text-secondary mb-4">{{ \Illuminate\Support\Str::limit($campaign->description, 115) }}</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between gap-2 small"><span class="text-secondary">{{ __('Terkumpul') }}</span><strong>Rp{{ number_format($campaign->collected_amount, 0, ',', '.') }}</strong></div>
                        <div class="progress mt-2" role="progressbar" aria-label="Progres donasi" aria-valuenow="{{ $campaign->progress }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-success" style="width:{{ $campaign->progress }}%"></div></div>
                        <div class="d-flex justify-content-between text-secondary small mt-2"><span>{{ __('Target') }} Rp{{ number_format($campaign->target_amount, 0, ',', '.') }}</span><span>{{ $campaign->progress }}%</span></div>
                        <a class="btn btn-outline-forest w-100 mt-3" href="{{ route('campaigns.show', $campaign->slug) }}">{{ __('Lihat cerita & kebutuhan') }} <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </article>
        </div>
    @empty
        <div class="col-12"><div class="card p-5 text-center">
            <i class="bi bi-journal-richtext display-5 text-secondary"></i>
            <h2 class="h5 mt-3">{{ __('Belum ada kampanye aktif') }}</h2>
            <p class="text-secondary mb-0">{{ __('Kampanye yang sudah disetujui admin akan muncul di sini.') }}</p>
        </div></div>
    @endforelse
</div>

<div class="mt-4">{{ $campaigns->links() }}</div>
@endsection
