<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Nooriallah Qayoumi',
            'email' => 'nooriallah18@egmail.com',
            'password'=> Hash::make('12345'),
            'role' => 'admin',
        ]);
    }
}
