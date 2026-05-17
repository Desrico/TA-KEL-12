<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Konselor;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'konselor',
            ]
        );

        $admin = User::where('email', 'admin@gmail.com')->first();

        if ($admin) {
            Konselor::updateOrCreate(
                ['user_id' => $admin->id],
                ['spesialisasi' => 'Admin']
            );
        }
    }
}
