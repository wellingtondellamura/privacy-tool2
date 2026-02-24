<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('answer'); // Suficiente, Insuficiente, Inexistente, Outro, Apropriado, Necessita melhorias, Inapropriado
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->unique(['inspection_id', 'question_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
