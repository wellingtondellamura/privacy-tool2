<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // null = consolidated
            $table->json('payload_json');
            $table->timestamps();

            $table->unique(['inspection_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_snapshots');
    }
};
