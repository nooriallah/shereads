<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Idempotent: safe to run `php artisan db:seed` repeatedly.
        if (! User::where('email', 'nooriallah18@gmail.com')->exists()) {
            User::factory()->create([
                'full_name' => 'Nooriallah Qayoumi',
                'email' => 'nooriallah18@gmail.com',
                'password' => Hash::make('12341234'),
                'role' => UserRole::SUPER_ADMIN->value,
            ]);
        }

        $this->call([
            QuestionnaireSeeder::class,
        ]);
    }
}
