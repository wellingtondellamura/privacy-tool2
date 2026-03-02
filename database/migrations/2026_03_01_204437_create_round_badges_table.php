<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('round_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_round_id')->unique()->constrained()->onDelete('cascade');
            $table->string('public_token', 64)->unique();
            $table->enum('style', ['default', 'compact', 'minimal'])->default('default');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('round_badges');
    }
};
