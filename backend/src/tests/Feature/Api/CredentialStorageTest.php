<?php

use App\Enums\GameServer;
use App\Enums\OrderStatus;
use App\Models\JokiCategory;
use App\Models\JokiPackage;
use App\Models\Order;
use App\Models\OrderGameAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function orderForCredentialTest(): Order
{
    $category = JokiCategory::create(['name' => 'Kat', 'slug' => 'kat', 'is_active' => true]);
    $package = JokiPackage::create([
        'category_id' => $category->id,
        'name' => 'Paket',
        'price' => 100000,
        'estimated_hours' => 5,
        'is_active' => true,
    ]);

    return Order::create([
        'order_no' => 'PMS-2026-001',
        'user_id' => User::factory()->create()->id,
        'package_id' => $package->id,
        'status' => OrderStatus::PendingPayment,
        'price_snapshot' => 100000,
        'total_amount' => 100000,
        'est_hours_snapshot' => 5,
        'queue_priority' => 0,
    ]);
}

it('menyimpan kredensial terenkripsi dan bisa dibaca kembali', function () {
    $order = orderForCredentialTest();

    OrderGameAccount::create([
        'order_id' => $order->id,
        'uid' => '812345678',
        'server' => GameServer::TwHkMo,
        'credential_encrypted' => ['username' => 'akun_genshin', 'password' => 'rahasia 123'],
    ]);

    $raw = (string) DB::table('order_game_accounts')->where('order_id', $order->id)->value('credential_encrypted');

    expect($raw)->not->toContain('akun_genshin')
        ->and($raw)->not->toContain('rahasia');

    $account = OrderGameAccount::findOrFail($order->id);

    expect($account->credential_encrypted)->toBe(['username' => 'akun_genshin', 'password' => 'rahasia 123'])
        ->and($account->server->value)->toBe('twhkmo');
});

it('tidak ikut tampil saat model di-serialize', function () {
    $order = orderForCredentialTest();

    $account = OrderGameAccount::create([
        'order_id' => $order->id,
        'uid' => '812345678',
        'server' => GameServer::Asia,
        'credential_encrypted' => ['username' => 'akun_genshin', 'password' => 'rahasia 123'],
    ]);

    expect($account->toArray())->not->toHaveKey('credential_encrypted');
});
