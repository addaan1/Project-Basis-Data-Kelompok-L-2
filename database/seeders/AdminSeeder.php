<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@warungpadi.com'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('password'),
                'peran' => 'admin',
                'saldo' => 10000000,
            ]
        );
    }
}
