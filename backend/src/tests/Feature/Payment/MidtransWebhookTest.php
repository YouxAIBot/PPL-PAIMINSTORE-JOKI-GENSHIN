<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\ActivityLog;
use App\Models\JokiCategory;
use App\Models\JokiPackage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

const MIDTRANS_SERVER_KEY = 'SB-Mid-server-TestKey123';

beforeEach(function () {
    config(['services.midtrans.server_key' => MIDTRANS_SERVER_KEY]);
});

/**
 * Payload notifikasi Midtrans sesuai bentuk aslinya.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function midtransPayload(array $overrides = []): array
{
    return array_merge([
        'order_id' => 'ORD-TEST-001',
        'status_code' => '200',
        'gross_amount' => '150000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'TRX-TEST-001',
    ], $overrides);
}

/**
 * Tambahkan signature_key yang benar sesuai aturan Midtrans.
 *
 * @param  array<string, mixed>  $payload
 * @return array<string, mixed>
 */
function withSignature(array $payload): array
{
    $payload['signature_key'] = hash(
        'sha512',
        $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . (string) config('services.midtrans.server_key')
    );

    return $payload;
}

/**
 * Buat satu order pending_payment beserta satu pembayaran pending.
 */
function createPendingOrder(string $orderNo = 'ORD-TEST-001', string $gatewayRef = 'TRX-TEST-001'): Order
{
    $user = User::factory()->create();

    $category = JokiCategory::create([
        'name' => 'Artefak',
        'slug' => 'artefak',
        'description' => 'Joki farming artefak',
        'is_active' => true,
    ]);

    $package = JokiPackage::create([
        'category_id' => $category->id,
        'name' => 'Paket Cepat',
        'description' => 'Selesai 1 hari',
        'price' => 150000,
        'estimated_hours' => 4,
        'is_active' => true,
    ]);

    $order = Order::create([
        'order_no' => $orderNo,
        'user_id' => $user->id,
        'package_id' => $package->id,
        'status' => OrderStatus::PendingPayment,
        'price_snapshot' => $package->price,
        'total_amount' => 150000,
        'est_hours_snapshot' => 4,
        'queue_priority' => 0,
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'gateway',
        'gateway_ref' => $gatewayRef,
        'amount' => 150000,
        'status' => PaymentStatus::Pending,
    ]);

    return $order;
}

it('menerima settlement: pembayaran lunas dan order masuk antrian', function () {
    $order = createPendingOrder();

    $response = $this->postJson('/api/v1/payments/midtrans/webhook', withSignature(midtransPayload()));

    $response->assertOk()->assertJson(['changed' => true]);

    $order->refresh();
    $payment = $order->payments()->first();

    expect($payment->status)->toBe(PaymentStatus::Paid)
        ->and($payment->paid_at)->not->toBeNull()
        ->and($order->status)->toBe(OrderStatus::Queued)
        ->and($order->paid_at)->not->toBeNull();

    // Setiap webhook yang mengubah status dicatat ke activity_logs
    expect(ActivityLog::where('action', 'payment.webhook')->count())->toBe(1);
});

it('bersifat idempoten: notifikasi ulang tidak mengubah status dua kali', function () {
    $order = createPendingOrder();

    $this->postJson('/api/v1/payments/midtrans/webhook', withSignature(midtransPayload()))
        ->assertOk();

    $second = $this->postJson('/api/v1/payments/midtrans/webhook', withSignature(midtransPayload()));

    $second->assertOk()->assertJson(['changed' => false]);

    $order->refresh();
    expect($order->status)->toBe(OrderStatus::Queued)
        ->and($order->payments()->first()->status)->toBe(PaymentStatus::Paid)
        ->and(ActivityLog::where('action', 'payment.webhook')->count())->toBe(1);
});

it('menolak notifikasi dengan signature yang salah', function () {
    $order = createPendingOrder();

    $payload = withSignature(midtransPayload());
    $payload['signature_key'] = str_repeat('0', 128);

    $this->postJson('/api/v1/payments/midtrans/webhook', $payload)->assertForbidden();

    $order->refresh();
    expect($order->status)->toBe(OrderStatus::PendingPayment)
        ->and($order->payments()->first()->status)->toBe(PaymentStatus::Pending)
        ->and(ActivityLog::count())->toBe(0);
});

it('menolak notifikasi ketika server key belum dikonfigurasi', function () {
    config(['services.midtrans.server_key' => '']);
    $order = createPendingOrder();

    $this->postJson('/api/v1/payments/midtrans/webhook', withSignature(midtransPayload()))
        ->assertForbidden();

    expect($order->refresh()->status)->toBe(OrderStatus::PendingPayment);
});

it('menandai pembayaran gagal dan membatalkan order saat transaksi expire', function () {
    $order = createPendingOrder();

    $payload = withSignature(midtransPayload([
        'transaction_status' => 'expire',
        'fraud_status' => null,
    ]));

    $this->postJson('/api/v1/payments/midtrans/webhook', $payload)
        ->assertOk()
        ->assertJson(['changed' => true]);

    $order->refresh();
    $payment = $order->payments()->first();

    expect($payment->status)->toBe(PaymentStatus::Failed)
        ->and($order->status)->toBe(OrderStatus::Cancelled)
        ->and($order->cancel_reason)->not->toBeNull();
});

it('membalas404 ketika order tidak ditemukan', function () {
    $payload = withSignature(midtransPayload(['order_id' => 'ORD-TIDAK-ADA']));

    $this->postJson('/api/v1/payments/midtrans/webhook', $payload)->assertNotFound();
});

it('membalas404 ketika order belum punya pembayaran', function () {
    $order = createPendingOrder();
    $order->payments()->delete();

    $this->postJson('/api/v1/payments/midtrans/webhook', withSignature(midtransPayload()))
        ->assertNotFound();
});

it('tidak mengubah status apa pun untuk notifikasi pending', function () {
    $order = createPendingOrder();

    $payload = withSignature(midtransPayload([
        'transaction_status' => 'pending',
        'fraud_status' => null,
    ]));

    $this->postJson('/api/v1/payments/midtrans/webhook', $payload)
        ->assertOk()
        ->assertJson(['changed' => false]);

    $order->refresh();
    expect($order->status)->toBe(OrderStatus::PendingPayment)
        ->and($order->payments()->first()->status)->toBe(PaymentStatus::Pending);
});

it('menolak payload yang tidak lengkap dengan422', function () {
    $this->postJson('/api/v1/payments/midtrans/webhook', ['order_id' => 'ORD-TEST-001'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status_code', 'gross_amount', 'signature_key', 'transaction_status']);
});
