@extends('layouts.main')

@section('content')
    <div class="row justify-content-center py-3">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-sm p-4 p-md-5">
                <a href="/" class="text-decoration-none text-center mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white mb-2" style="width:52px;height:52px;background:var(--api)"><i class="bi bi-fire fs-4"></i></span>
                    <div class="h4 fw-bold text-hutan mb-0">Peduli Karhutla</div>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
@endsection
