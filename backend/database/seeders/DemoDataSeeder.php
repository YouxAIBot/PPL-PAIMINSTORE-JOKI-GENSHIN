<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Conversation;
use App\Models\JokiCategory;
use App\Models\JokiPackage;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderGameAccount;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::updateOrCreate(
            ['email' => 'user@paiminstore.test'],
            ['name' => 'Pengguna Contoh', 'password' => 'password123', 'role' => 'user'],
        );

        $booster = User::where('email', 'booster1@paiminstore.test')->first();

        foreach ([
            'Daily Login' => 'Paket login harian, resin, dan reward event.',
            'Domain & Boss' => 'Farming material domain dan boss sesuai permintaan.',
            'Quest & Exploration' => 'Quest arsip, exploration, dan chest hunting.',
        ] as $name => $desc) {
            JokiCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $desc, 'is_active' => true],
            );
        }

        $categories = JokiCategory::all();
        $packages = collect();
        foreach ($categories as $i => $category) {
            $packages->push(JokiPackage::create([
                'category_id' => $category->id,
                'name' => 'Paket '.$category->name,
                'tier' => 'Dasar',
                'description' => $category->description,
                'price' => 50000 + ($i * 25000),
                'estimated_hours' => 6 + $i,
                'is_active' => true,
            ]));
        }

        $orderNo = now()->format('Ymd').'001';

        $make = function (string $status, ?User $assigned, int $idx) use ($customer, $packages, &$orderNo, $booster) {
            $package = $packages[$idx % $packages->count()];

            $order = Order::create([
                'order_no' => 'ORD-'.($orderNo + $idx),
                'user_id' => $customer->id,
                'package_id' => $package->id,
                'booster_id' => $assigned?->id ?? $booster?->id,
                'status' => $status,
                'price_snapshot' => $package->price,
                'total_amount' => $package->price,
                'est_hours_snapshot' => $package->estimated_hours,
                'queue_priority' => $idx,
                'paid_at' => in_array($status, [Order::QUEUED, Order::IN_PROGRESS, Order::COMPLETED], true) ? now()->subDays(2) : null,
                'started_at' => in_array($status, [Order::IN_PROGRESS, Order::COMPLETED], true) ? now()->subDay() : null,
                'completed_at' => $status === Order::COMPLETED ? now() : null,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'gateway_ref' => $order->order_no.'-1',
                'method' => $status === Order::PENDING_PAYMENT ? 'manual' : 'midtrans',
                'amount' => $package->price,
                'status' => match ($status) {
                    Order::PENDING_PAYMENT => Payment::PENDING,
                    Order::CANCELLED => Payment::FAILED,
                    default => Payment::PAID,
                },
                'verified_by' => $status === Order::PENDING_PAYMENT ? null : 1,
                'paid_at' => $order->paid_at,
            ]);

            return $order;
        };

        $orders = [
            $make(Order::PENDING_PAYMENT, null, 1),
            $make(Order::QUEUED, null, 2),
            $make(Order::IN_PROGRESS, $booster, 3),
            $make(Order::COMPLETED, $booster, 4),
        ];

        $done = $orders[3];
        OrderGameAccount::create([
            'order_id' => $done->id,
            'uid' => '801234567',
            'server' => 'asia',
            'credential_encrypted' => encrypt('player-login@example.com / P@ssw0rd!'),
            'attachment_url' => null,
            'purge_at' => now()->addDays(7),
        ]);
        Review::create([
            'order_id' => $done->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Cepat selesai, komunikasi jelas.',
        ]);

        $conversation = Conversation::create([
            'user_id' => $customer->id,
            'last_message_at' => now(),
            'admin_unread' => 1,
            'user_unread' => 0,
        ]);
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $customer->id,
            'body' => 'Halo admin, order saya kapan dikerjakan?',
            'created_at' => now(),
        ]);

        ActivityLog::record($booster, 'demo.seeded', meta: ['note' => 'data contoh']);
    }
}
