<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('membuat semua tabel dari ERD dan tabel pendukung', function () {
    $tables = [
        // ERD bagian 2.1 sampai 2.10
        'joki_categories',
        'joki_packages',
        'users',
        'orders',
        'order_game_accounts',
        'payments',
        'conversations',
        'messages',
        'reviews',
        'activity_logs',
        // Tabel pendukung (bagian 2.11, 2.12, dan bawaan Laravel)
        'notifications',
        'device_tokens',
        'password_reset_tokens',
        'sessions',
        'cache',
        'jobs',
    ];

    foreach ($tables as $table) {
        expect(Schema::hasTable($table))->toBeTrue("Tabel {$table} belum dibuat");
    }
});

it('memiliki kolom kunci sesuai ERD', function () {
    $columns = [
        'users' => ['name', 'email', 'password', 'avatar_url', 'role', 'status', 'show_on_leaderboard', 'email_verified_at'],
        'joki_categories' => ['name', 'slug', 'description', 'is_active'],
        'joki_packages' => ['category_id', 'name', 'price', 'estimated_hours', 'is_active'],
        'orders' => [
            'order_no', 'user_id', 'package_id', 'booster_id', 'status',
            'price_snapshot', 'total_amount', 'est_hours_snapshot', 'queue_priority',
            'customer_notes', 'cancel_reason', 'paid_at', 'started_at', 'completed_at',
        ],
        'order_game_accounts' => ['order_id', 'uid', 'server', 'credential_encrypted', 'attachment_url', 'purge_at'],
        'payments' => [
            'order_id', 'method', 'gateway_ref', 'amount', 'status', 'proof_url',
            'refunded_amount', 'verified_by', 'paid_at', 'refunded_at',
        ],
        'conversations' => ['user_id', 'last_message_at', 'admin_unread', 'user_unread'],
        'messages' => ['conversation_id', 'sender_id', 'order_id', 'body', 'attachment_url', 'read_at'],
        'reviews' => ['order_id', 'user_id', 'rating', 'comment'],
        'activity_logs' => ['actor_id', 'action', 'entity_type', 'entity_id', 'meta', 'ip'],
        'device_tokens' => ['user_id', 'token', 'platform', 'last_used_at'],
    ];

    foreach ($columns as $table => $expected) {
        expect(Schema::hasColumns($table, $expected))
            ->toBeTrue("Kolom {$table} tidak lengkap: " . implode(', ', $expected));
    }
});

it('menambahkan created_at dan updated_at di semua tabel aplikasi', function () {
    $tables = [
        'joki_categories', 'joki_packages', 'users', 'orders', 'order_game_accounts',
        'payments', 'conversations', 'messages', 'reviews', 'activity_logs', 'device_tokens',
    ];

    foreach ($tables as $table) {
        expect(Schema::hasColumns($table, ['created_at', 'updated_at']))
            ->toBeTrue("Tabel {$table} belum punya timestamps");
    }
});
