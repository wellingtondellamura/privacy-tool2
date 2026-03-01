<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('round_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_round_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('visibility')->default('private');
            $table->string('slug')->unique();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('score')->default(0)->index();
            $table->string('medal')->nullable()->index();
            $table->integer('year')->nullable()->index();
            $table->foreignId('questionnaire_version_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('round_publications');
    }
};
