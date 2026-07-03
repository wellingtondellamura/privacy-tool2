<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidated_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('final_answer', 20); // 'high' / 'medium' / 'low' / 'other'
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('resolution_method', 20); // 'owner' / 'convergence' / 'majority'
            $table->timestamps();

            // Prevent duplicate entries for the same question in a round
            $table->unique(['evaluation_round_id', 'question_id'], 'round_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidated_responses');
    }
};
