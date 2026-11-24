<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Audit\ActivityLogger;

/**
 * Penerima webhook Midtrans (DFD proses 3.2).
 *
 * Aturan (dokumen fondasi bagian 5.3):
 *  - verifikasi signature SHA-512: sha512(order_id + status_code + gross_amount + server_key);
 *  - idempoten: pembayaran berstatus final tidak boleh berubah lagi;
 *  - settlement/capture -> paid + order queued; deny/cancel/failure/expire -> failed + order cancelled;
 *  - setiap penerimaan dicatat ke activity_logs.
 */
class MidtransWebhookService
{
    public function __construct(private ActivityLogger $activityLogger) {}

    /**
     * @param  array<string, mixed>  $payload  payload notifikasi Midtrans yang sudah divalidasi
     * @param  string|null  $ip  IP pengirim untuk audit
     * @return array{code: int, body: array<string, mixed>}
     */
    public function process(array $payload, ?string $ip = null): array
    {
        if (! $this->signatureIsValid($payload)) {
            return ['code' => 403, 'body' => ['message' => 'Signature tidak valid']];
        }

        $order = Order::query()->where('order_no', $payload['order_id'])->first();
        if (! $order) {
            return ['code' => 404, 'body' => ['message' => 'Order tidak ditemukan']];
        }

        $payment = $this->resolvePayment($order, $payload);
        if (! $payment) {
            return ['code' => 404, 'body' => ['message' => 'Pembayaran tidak ditemukan']];
        }

        // Idempotensi: status final tidak boleh berubah lagi oleh notifikasi ulang.
        if ($payment->status->isFinal()) {
            return [
                'code' => 200,
                'body' => ['message' => 'Pembayaran sudah diproses sebelumnya', 'changed' => false],
            ];
        }

        $target = $this->mapStatus($payload);

        // Notifikasi pending: belum ada perubahan status, tetap dibalas 200 agar Midtrans berhenti mengulang.
        if ($target === null) {
            return ['code' => 200, 'body' => ['message' => 'Menunggu pembayaran', 'changed' => false]];
        }

        $payment->status = $target;
        if ($target === PaymentStatus::Paid) {
            $payment->paid_at = now();
        }
        $payment->save();

        $orderChanged = false;
        if ($order->status === OrderStatus::PendingPayment) {
            if ($target === PaymentStatus::Paid) {
                $order->status = OrderStatus::Queued;
                $order->paid_at = now();
                $orderChanged = true;
            } elseif ($target === PaymentStatus::Failed) {
                $order->status = OrderStatus::Cancelled;
                $order->cancel_reason = 'Pembayaran gagal (' . $payload['transaction_status'] . ')';
                $orderChanged = true;
            }
        }
        if ($orderChanged) {
            $order->save();
        }

        $this->activityLogger->record(
            actorId: null,
            action: 'payment.webhook',
            entityType: 'payment',
            entityId: $payment->getKey(),
            meta: [
                'order_no' => $order->order_no,
                'transaction_status' => $payload['transaction_status'],
                'transaction_id' => $payload['transaction_id'] ?? null,
                'payment_status' => $target->value,
                'order_status' => $order->status->value,
            ],
            ip: $ip,
        );

        return ['code' => 200, 'body' => ['message' => 'OK', 'changed' => true]];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function signatureIsValid(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');

        // Kunci belum diisi di .env: semua notifikasi ditolak, tidak ada jalan pintas.
        if ($serverKey === '') {
            return false;
        }

        $expected = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey
        );

        return hash_equals($expected, (string) $payload['signature_key']);
    }

    /**
     * Cari pembayaran yang dimaksud notifikasi: cocokkan gateway_ref dengan
     * transaction_id, lalu order_no, lalu pembayaran terakhir order tersebut.
     *
     * @param  array<string, mixed>  $payload
     */
    private function resolvePayment(Order $order, array $payload): ?Payment
    {
        $payments = $order->payments();

        if (! empty($payload['transaction_id'])) {
            $payment = (clone $payments)
                ->where('gateway_ref', $payload['transaction_id'])
                ->latest('id')
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        $payment = (clone $payments)
            ->where('gateway_ref', $payload['order_id'])
            ->latest('id')
            ->first();

        return $payment ?? $payments->latest('id')->first();
    }

    /**
     * Pemetaan status transaksi Midtrans ke payments.status.
     * Mengembalikan null untuk notifikasi yang belum menentukan (pending).
     *
     * @param  array<string, mixed>  $payload
     */
    private function mapStatus(array $payload): ?PaymentStatus
    {
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? null;

        if ($fraudStatus === 'deny') {
            return PaymentStatus::Failed;
        }

        if (in_array($transactionStatus, ['settlement', 'capture'], true)) {
            return PaymentStatus::Paid;
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'failure', 'expire'], true)) {
            return PaymentStatus::Failed;
        }

        return null;
    }
}
