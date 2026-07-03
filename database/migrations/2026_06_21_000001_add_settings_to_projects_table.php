<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('require_evidence_for_high')->default(false);
            $table->string('consensus_model', 30)->default('owner_decides');
            $table->boolean('is_self_assessment')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['require_evidence_for_high', 'consensus_model', 'is_self_assessment']);
        });
    }
};
