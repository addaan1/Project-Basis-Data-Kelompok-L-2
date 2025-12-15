<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder {
    public function run(): void {
        // Create Petani (Seller)
        User::firstOrCreate(
            ['email' => 'petani@warungpadi.com'],
            [
                'nama' => 'Pak Budi Petani',
                'peran' => 'petani',
                'password' => Hash::make('password'),
                'saldo' => 0,
            ]
        );

        // Create Pengepul
        User::firstOrCreate(
            ['email' => 'pengepul@warungpadi.com'],
            [
                'nama' => 'Bu Siti Pengepul',
                'peran' => 'pengepul',
                'password' => Hash::make('password'),
                'saldo' => 5000000,
            ]
        );

        // Create Pasar (Market)
        User::firstOrCreate(
            ['email' => 'pasar@warungpadi.com'],
            [
                'nama' => 'Pasar Induk',
                'peran' => 'pasar',
                'password' => Hash::make('password'),
                'saldo' => 100000000,
            ]
        );

        // Create Regular User (Distributor/Pembeli)
        User::firstOrCreate(
            ['email' => 'pembeli@warungpadi.com'],
            [
                'nama' => 'Andi Pembeli',
                'peran' => 'distributor', 
                'password' => Hash::make('password'),
                'saldo' => 1000000,
            ]
        );
    }
}