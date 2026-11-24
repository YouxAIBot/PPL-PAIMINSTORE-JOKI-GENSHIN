<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ERD bagian 2.6: payments
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->enum('method', ['gateway', 'manual'])->default('gateway');
            // Idempotensi webhook Midtrans
            $table->string('gateway_ref')->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('proof_url')->nullable();
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('refunded_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
