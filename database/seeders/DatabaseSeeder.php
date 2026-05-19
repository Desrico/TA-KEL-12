<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Data dasar untuk uji admin dan laporan konseling.
        $this->call([
            AdminSeeder::class,
            ExampleCompletedSessionSeeder::class,
            FransCompletedSessionSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'nama' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
    }
}
