<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder {
    public function run(): void {
        // Create Petani (Seller)
        DB::table('users')->insert([
            'id_user' => 2,
            'nama' => 'Pak Budi Petani',
            'email' => 'petani@warungpadi.com',
            'peran' => 'petani',
            'password' => Hash::make('password'),
            'saldo' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Pengepul
        DB::table('users')->insert([
            'id_user' => 3,
            'nama' => 'Bu Siti Pengepul',
            'email' => 'pengepul@warungpadi.com',
            'peran' => 'pengepul',
            'password' => Hash::make('password'),
            'saldo' => 5000000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Pasar (Market)
        DB::table('users')->insert([
            'id_user' => 4,
            'nama' => 'Pasar Induk',
            'email' => 'pasar@warungpadi.com',
            'peran' => 'pasar',
            'password' => Hash::make('password'),
            'saldo' => 100000000, // Large specific ID for logic
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Regular User (Buyer)
        DB::table('users')->insert([
            'id_user' => 5,
            'nama' => 'Andi Pembeli',
            'email' => 'pembeli@warungpadi.com',
            'peran' => 'distributor', // Using distributor as 'regular' for now based on enum
            'password' => Hash::make('password'),
            'saldo' => 1000000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}