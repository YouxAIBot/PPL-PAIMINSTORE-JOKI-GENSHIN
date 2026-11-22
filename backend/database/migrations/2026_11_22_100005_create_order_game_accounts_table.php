<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_game_accounts', function (Blueprint $table) {
            $table->foreignId('order_id')->primary()->constrained('orders')->cascadeOnDelete();
            $table->string('uid');
            $table->enum('server', ['asia', 'europe', 'america', 'twhkmo']);
            $table->text('credential_encrypted');
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
