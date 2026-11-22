<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('joki_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('joki_categories');
            $table->string('name');
            $table->string('tier')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('estimated_hours')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('joki_packages');
    }
};
