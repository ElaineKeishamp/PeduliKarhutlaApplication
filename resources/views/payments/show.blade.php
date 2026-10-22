@extends('layouts.main')

@php($isMidtrans = $donation->payment_gateway === 'midtrans')

@section('title', __('Checkout pembayaran').' — Peduli Karhutla')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="mb-3"><a href="{{ route('campaigns.show', $donation->campaign->slug) }}" class="text-hutan text-decoration-none"><i class="bi bi-arrow-left me-1"></i>{{ __('Kembali ke kampanye') }}</a></div>
        <div class="card p-4 p-lg-5">
            <div class="text-center mb-4">
                @php($isSuccess = $donation->status === 'success')
                @php($isFailed = $donation->status === 'failed')
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 {{ $isSuccess ? 'bg-success-subtle text-success' : ($isFailed ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }}" style="width:64px;height:64px"><i class="bi {{ $isSuccess ? 'bi-check-lg' : ($isFailed ? 'bi-x-lg' : 'bi-shield-check') }} fs-2"></i></span>
                <h1 class="h3 text-hutan">
                    @if ($isSuccess) {{ __('Pembayaran berhasil') }}
                    @elseif ($isFailed) {{ __('Pembayaran gagal atau kedaluwarsa') }}
                    @elseif ($isMidtrans) {{ __('Menunggu pembayaran') }}
                    @else {{ __('Checkout pembayaran simulasi') }}
                    @endif
                </h1>
                <p class="text-secondary mb-0">
                    @if ($isSuccess) {{ __('Donasimu sudah masuk ke progres kampanye.') }}
                    @elseif ($isFailed) {{ __('Donasi ini belum masuk ke progres kampanye. Kamu bisa mencoba berdonasi lagi.') }}
                    @elseif ($isMidtrans) {{ __('Selesaikan pembayaran pada jendela Midtrans Sandbox. Status akan diverifikasi oleh server.') }}
                    @else {{ __('Selesaikan langkah simulasi berikut untuk mencatat donasimu.') }}
                    @endif
                </p>
            </div>

            @if ($isMidtrans)
                <div class="alert alert-info small"><i class="bi bi-shield-check me-1"></i>{{ __('Midtrans Sandbox untuk pengujian. Tidak ada uang sungguhan yang ditagihkan.') }}</div>
            @else
                <div class="alert alert-warning small"><i class="bi bi-info-circle me-1"></i>{{ __('Simulasi lokal saja. Tidak terhubung ke Midtrans, bank, QRIS, atau e-wallet sungguhan.') }}</div>
            @endif

            <div class="rounded-3 bg-light p-3 mb-4">
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom"><span class="text-secondary">{{ __('Nomor tagihan') }}</span><strong>{{ $donation->invoice_no }}</strong></div>
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom"><span class="text-secondary">{{ __('Kampanye') }}</span><strong class="text-end">{{ $donation->campaign->title }}</strong></div>
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom"><span class="text-secondary">{{ __('Metode pembayaran') }}</span><strong>{{ __(['qris' => 'QRIS', 'transfer' => 'Transfer bank', 'ewallet' => 'E-wallet'][$donation->payment_method]) }}{{ $isMidtrans ? ' · Midtrans Sandbox' : ' · simulasi' }}</strong></div>
                <div class="d-flex justify-content-between gap-3 pt-3"><span class="text-secondary">{{ __('Total') }}</span><strong class="fs-5 text-hutan">Rp{{ number_format($donation->amount, 0, ',', '.') }}</strong></div>
            </div>

            @if ($donation->status === 'pending' && $isMidtrans)
                @if ($donation->snap_token)
                    <button type="button" id="open-midtrans" class="btn btn-api btn-lg w-100 mb-2"><i class="bi bi-lock me-1"></i>{{ __('Buka pembayaran Midtrans') }}</button>
                    <form id="refresh-midtrans-status" method="POST" action="{{ route('payments.midtrans.refresh', $donation->invoice_no) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-success w-100">{{ __('Periksa status pembayaran') }}</button>
                    </form>
                    <p class="small text-secondary text-center mb-0">{{ __('Gunakan metode dan data uji Sandbox dari Midtrans. Jangan masukkan data kartu sungguhan.') }}</p>
                @else
                    <p class="small text-secondary">{{ __('Sesi pembayaran belum berhasil dibuat. Pastikan Sandbox Key sudah diatur, lalu coba lagi.') }}</p>
                    <form method="POST" action="{{ route('payments.midtrans.token', $donation->invoice_no) }}">
                        @csrf
                        <button type="submit" class="btn btn-api btn-lg w-100"><i class="bi bi-arrow-clockwise me-1"></i>{{ __('Coba buat sesi pembayaran lagi') }}</button>
                    </form>
                @endif
            @elseif ($donation->status === 'pending' && ! $isMidtrans)
                @if ($donation->payment_method === 'qris')
                    <div class="text-center border rounded-3 p-4 mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white border rounded-3 p-3 mb-2" style="width:150px;height:150px"><i class="bi bi-qr-code display-2 text-secondary"></i></div>
                        <div class="small text-secondary">{{ __('QR demo (tidak dapat dipindai)') }}</div>
                    </div>
                @elseif ($donation->payment_method === 'transfer')
                    <div class="border rounded-3 p-3 mb-4"><div class="small text-secondary">{{ __('Informasi rekening demo') }}</div><strong>Bank Simulasi · 0000 1234 5678</strong><div class="small text-secondary">{{ __('a.n. Peduli Karhutla Demo — jangan transfer uang sungguhan') }}</div></div>
                @else
                    <div class="border rounded-3 p-3 mb-4"><div class="small text-secondary">{{ __('E-wallet demo') }}</div><strong>{{ __('Metode pembayaran demo siap') }}</strong><div class="small text-secondary">{{ __('Tidak membuka aplikasi atau menagih saldo e-wallet.') }}</div></div>
                @endif
                <form method="POST" action="{{ route('payments.simulate', $donation->invoice_no) }}">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100"><i class="bi bi-check-circle me-1"></i>{{ __('Simulasikan pembayaran berhasil') }}</button>
                </form>
            @elseif ($isSuccess)
                <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ __('Pembayaran tercatat pada') }} {{ $donation->paid_at?->format('d/m/Y H:i') }}.</div>
                @if ($donation->certificate)
                    <a href="{{ route('certificates.show', $donation->certificate->certificate_no) }}" class="btn btn-outline-success w-100 mb-2"><i class="bi bi-award me-1"></i>{{ __('Lihat / cetak sertifikat donatur') }}</a>
                @endif
                <a href="{{ route('campaigns.show', $donation->campaign->slug) }}" class="btn btn-api w-100">{{ __('Kembali dan lihat progres donasi') }}</a>
            @else
                <a href="{{ route('campaigns.show', $donation->campaign->slug) }}" class="btn btn-api w-100">{{ __('Kembali ke kampanye') }}</a>
            @endif
        </div>
    </div>
</div>
@endsection

@if ($isMidtrans && $donation->status === 'pending' && $donation->snap_token)
    @push('scripts')
        <script src="{{ app(\App\Services\MidtransPaymentService::class)->snapScriptUrl() }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script>
            document.getElementById('open-midtrans')?.addEventListener('click', function () {
                window.snap.pay(@json($donation->snap_token), {
                    onSuccess: checkMidtransStatus,
                    onPending: checkMidtransStatus,
                    onError: checkMidtransStatus,
                });
            });

            function checkMidtransStatus() {
                document.getElementById('refresh-midtrans-status')?.requestSubmit();
            }
        </script>
    @endpush
@endif
