<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ERD bagian 2.5: order_game_accounts (relasi 1:1 dengan orders)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_game_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->primary();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('uid');
            $table->enum('server', ['asia', 'america', 'europe', 'tw_hk_mo']);
            // Diisi terenkripsi AES-256-GCM, tidak pernah dikirim balik lewat API
            $table->binary('credential_encrypted');
            $table->string('attachment_url')->nullable();
            $table->timestampTz('purge_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_game_accounts');
    }
};
