@extends('layouts.main')

@section('content')
    @isset($header)
        <div class="mb-4"><h1 class="h3 text-hutan mb-0">{{ $header }}</h1></div>
    @endisset
    {{ $slot }}
@endsection
