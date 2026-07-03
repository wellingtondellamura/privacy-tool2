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
        Schema::table('users', function (Blueprint $table) {
            // User profile type: student, professional or researcher (optional)
            $table->string('profile')->nullable()->after('locale');
            // User institutional or professional affiliation (optional)
            $table->string('affiliation')->nullable()->after('profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile', 'affiliation']);
        });
    }
};
