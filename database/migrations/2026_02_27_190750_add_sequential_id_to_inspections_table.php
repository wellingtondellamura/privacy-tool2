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
        Schema::table('inspections', function (Blueprint $table) {
            $table->unsignedInteger('sequential_id')->nullable()->after('project_id');
            $table->unique(['project_id', 'sequential_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropUnique(['project_id', 'sequential_id']);
            $table->dropColumn('sequential_id');
        });
    }
};
