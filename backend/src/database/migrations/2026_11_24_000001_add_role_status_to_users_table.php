<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom users dari ERD bagian 2.3 yang belum ada:
 * role, status, show_on_leaderboard. Nilai enum lihat bagian 5.2 dokumen fondasi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'booster', 'admin'])->default('customer');
            $table->enum('status', ['active', 'banned'])->default('active');
            $table->boolean('show_on_leaderboard')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'show_on_leaderboard']);
        });
    }
};
