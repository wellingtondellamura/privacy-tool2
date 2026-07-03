<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->json('tooltip')->nullable();
            $table->json('good_practice_example')->nullable();
            $table->json('bad_practice_example')->nullable();
        });

        DB::table('questions')->update([
            'tooltip' => json_encode(['pt_BR' => 'Em construção', 'en' => 'Under construction']),
            'good_practice_example' => json_encode(['pt_BR' => 'Em construção', 'en' => 'Under construction']),
            'bad_practice_example' => json_encode(['pt_BR' => 'Em construção', 'en' => 'Under construction']),
        ]);
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['tooltip', 'good_practice_example', 'bad_practice_example']);
        });
    }
};
