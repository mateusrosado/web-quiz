<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\Configuration;

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
            'name' => 'Mateus',
            'email' => 'm@te.us',
            'password' => Hash::make('admin123'),
            'is_admin' => true
        ]);
        Configuration::set('quiz_question_limit', 10);
        Configuration::set('score_per_question', 10);
    }
}
