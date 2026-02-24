<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Wellington',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'), // password
            'email_verified_at' => now(), // verified so user can login directly
        ]);

        $this->call([
            QuestionnaireV1Seeder::class,
        ]);
    }
}
