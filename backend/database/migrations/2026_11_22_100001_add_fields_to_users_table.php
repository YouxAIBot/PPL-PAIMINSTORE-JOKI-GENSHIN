<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->nullable();
            $table->enum('role', ['user', 'booster', 'admin'])->default('user');
            $table->enum('status', ['active', 'banned'])->default('active');
            $table->boolean('show_on_leaderboard')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'role', 'status', 'show_on_leaderboard']);
        });
    }
};
