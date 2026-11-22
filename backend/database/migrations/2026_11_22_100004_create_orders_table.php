<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('package_id')->constrained('joki_packages');
            $table->foreignId('booster_id')->nullable()->constrained('users');
            $table->enum('status', ['pending_payment', 'paid', 'queued', 'in_progress', 'completed', 'cancelled', 'refunded'])->default('pending_payment');
            $table->decimal('price_snapshot', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->unsignedInteger('est_hours_snapshot');
            $table->integer('queue_priority')->default(0);
            $table->text('customer_notes')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'queue_priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
