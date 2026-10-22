<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Certificate;
use App\Models\Donation;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransPaymentService
{
    private function serverKey(): string
    {
        return (string) config('services.midtrans.server_key');
    }

    public function enabled(): bool
    {
        return (bool) config('services.midtrans.enabled')
            && filled(config('services.midtrans.server_key'))
            && filled(config('services.midtrans.client_key'));
    }

    public function snapScriptUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /** @throws RequestException|RuntimeException */
    public function createSnapToken(Donation $donation, Request $request): string
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Midtrans Sandbox belum dikonfigurasi.');
        }

        $donation->loadMissing(['user', 'campaign']);
        $customerDetails = [
            'first_name' => $donation->user->name,
            'email' => $donation->user->email,
        ];
        if ($donation->user->phone) {
            $customerDetails['phone'] = $donation->user->phone;
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $donation->invoice_no,
                'gross_amount' => (int) $donation->amount,
            ],
            'customer_details' => $customerDetails,
            'item_details' => [[
                'id' => 'donasi-'.$donation->campaign_id,
                'price' => (int) $donation->amount,
                'quantity' => 1,
                'name' => mb_substr('Donasi: '.$donation->campaign->title, 0, 50),
            ]],
            'enabled_payments' => $this->enabledPayments($donation->payment_method),
            'callbacks' => [
                'finish' => route('payments.show', $donation->invoice_no, true),
            ],
        ];

        $endpoint = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $response = Http::timeout(25)
            ->withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->withHeaders([
                'X-Override-Notification' => route('payments.midtrans.notification', [], true),
            ])
            ->post($endpoint, $payload);

        $response->throw();
        $token = $response->json('token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Midtrans tidak mengirim token pembayaran.');
        }

        return $token;
    }

    /** Verify Midtrans status server-to-server; browser callbacks are never trusted as payment proof. */
    public function refreshStatus(Donation $donation): Donation
    {
        if (! $this->enabled() || $donation->payment_gateway !== 'midtrans') {
            throw new RuntimeException('Transaksi ini bukan pembayaran Midtrans yang aktif.');
        }

        $baseUrl = config('services.midtrans.is_production')
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        $status = Http::timeout(20)
            ->withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->get($baseUrl.urlencode($donation->invoice_no).'/status')
            ->throw()
            ->json();

        if (($status['order_id'] ?? null) !== $donation->invoice_no
            || ! $this->sameAmount($status['gross_amount'] ?? null, $donation->amount)) {
            throw new RuntimeException('Data status Midtrans tidak cocok dengan tagihan aplikasi.');
        }

        return $this->applyStatus(
            $donation,
            (string) ($status['transaction_status'] ?? ''),
            $status['fraud_status'] ?? null,
            $status['transaction_id'] ?? null,
        );
    }

    /** Verify webhook signature, then retrieve authoritative transaction status from Midtrans. */
    public function handleNotification(array $payload): Donation
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signature = (string) ($payload['signature_key'] ?? '');

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey());
        if ($orderId === '' || $signature === '' || ! hash_equals($expected, $signature)) {
            throw new \InvalidArgumentException('Tanda tangan notifikasi Midtrans tidak valid.');
        }

        $donation = Donation::where('invoice_no', $orderId)
            ->where('payment_gateway', 'midtrans')
            ->firstOrFail();

        return $this->refreshStatus($donation);
    }

    private function enabledPayments(string $method): array
    {
        return match ($method) {
            // Snap identifies QR-based wallet payments as GoPay/ShopeePay,
            // not as a generic `qris` channel.
            'qris' => ['gopay', 'shopeepay'],
            'transfer' => ['bank_transfer'],
            'ewallet' => ['gopay', 'shopeepay', 'dana', 'ovo', 'linkaja'],
            default => ['qris', 'bank_transfer', 'gopay'],
        };
    }

    private function sameAmount(mixed $grossAmount, int $amount): bool
    {
        return is_numeric($grossAmount)
            && number_format((float) $grossAmount, 2, '.', '') === number_format($amount, 2, '.', '');
    }

    private function applyStatus(Donation $donation, string $providerStatus, ?string $fraudStatus, ?string $transactionId): Donation
    {
        $newStatus = match ($providerStatus) {
            'settlement' => 'success',
            'capture' => $fraudStatus === 'accept' ? 'success' : ($fraudStatus === 'deny' ? 'failed' : 'pending'),
            'pending' => 'pending',
            'deny', 'cancel', 'expire', 'failure', 'refund' => 'failed',
            default => null,
        };

        if ($newStatus === null) {
            return $donation->fresh(['campaign', 'user', 'certificate']);
        }

        return DB::transaction(function () use ($donation, $newStatus, $providerStatus, $transactionId) {
            $locked = Donation::whereKey($donation->id)->lockForUpdate()->firstOrFail();
            $previous = $locked->status;
            $isRefund = $providerStatus === 'refund';

            // Do not allow a delayed pending/failed notification to downgrade a paid donation.
            if ($previous === 'success' && ! $isRefund) {
                return $locked->fresh(['campaign', 'user', 'certificate']);
            }

            if ($previous !== 'success' && $newStatus === 'success') {
                $campaign = Campaign::whereKey($locked->campaign_id)->lockForUpdate()->firstOrFail();
                $campaign->increment('collected_amount', $locked->amount);
                Certificate::firstOrCreate(
                    ['donation_id' => $locked->id],
                    [
                        'certificate_no' => 'PK-'.now()->format('Ymd').'-'.strtoupper(str()->random(8)),
                        'issued_at' => now(),
                    ],
                );
            } elseif ($previous === 'success' && $isRefund) {
                Campaign::whereKey($locked->campaign_id)->decrement('collected_amount', $locked->amount);
                $locked->certificate()->delete();
            }

            $locked->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'success' ? ($locked->paid_at ?? now()) : null,
                'payment_ref' => $transactionId ?: $locked->payment_ref,
            ]);

            return $locked->fresh(['campaign', 'user', 'certificate']);
        });
    }
}
