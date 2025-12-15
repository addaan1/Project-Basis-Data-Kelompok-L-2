<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProdukBeras;
use App\Models\Transaksi;
use App\Models\Negosiasi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Super Admin',
                'password' => Hash::make('password'),
                'peran' => 'admin',
                'saldo' => 0
            ]
        );

        $petani1 = User::firstOrCreate(
            ['email' => 'petani1@example.com'],
            [
                'nama' => 'Pak Budi (Petani)',
                'password' => Hash::make('password'),
                'peran' => 'petani',
                'saldo' => 5000000,
                'bank_name' => 'BRI',
                'account_number' => '1234567890',
                'account_name' => 'Budi Santoso'
            ]
        );

        $petani2 = User::firstOrCreate(
            ['email' => 'petani2@example.com'],
            [
                'nama' => 'Bu Siti (Petani)',
                'password' => Hash::make('password'),
                'peran' => 'petani',
                'saldo' => 2500000
            ]
        );

        $pengepul1 = User::firstOrCreate(
            ['email' => 'pengepul1@example.com'],
            [
                'nama' => 'CV Maju Jaya (Pengepul)',
                'password' => Hash::make('password'),
                'peran' => 'pengepul',
                'saldo' => 150000000
            ]
        );

        $pengepul2 = User::firstOrCreate(
            ['email' => 'pengepul2@example.com'],
            [
                'nama' => 'UD Beras Makmur (Pengepul)',
                'password' => Hash::make('password'),
                'peran' => 'pengepul',
                'saldo' => 80000000
            ]
        );

        // 2. Create Products
        $products = [];
        $types = ['Pandan Wangi', 'Rojolele', 'IR 64', 'Mentik Wangi'];
        
        // Legacy: Create Dummy Pasar
        $pasarId = DB::table('pasars')->insertGetId([
            'nama_pasar' => 'Pasar Induk',
            'id_user' => $admin->id_user,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 0; $i < 10; $i++) {
            $petani = ($i % 2 == 0) ? $petani1 : $petani2;
            $products[] = ProdukBeras::create([
                'id_petani' => $petani->id_user,
                'nama_produk' => 'Beras ' . $types[array_rand($types)] . ' Super',
                'jenis_beras' => $types[array_rand($types)],
                'kualitas' => 'Premium',
                'harga' => rand(12000, 15000),
                'stok' => rand(100, 5000),
                'deskripsi' => 'Beras berkualitas tinggi hasil panen terbaru.',
                'lokasi_gudang' => 'Gudang Utama',
                'nama_petani' => $petani->nama
            ]);
        }

        // 3. Create Transactions (Massive Data: 100 transactions)
        for ($i = 0; $i < 100; $i++) {
            // Force 20% of transactions to be TODAY (Realtime testing)
            $isToday = rand(1, 100) <= 20; 
            $date = $isToday ? Carbon::now() : Carbon::now()->subDays(rand(1, 60));
            
            $product = $products[array_rand($products)];
            $buyer = ($i % 2 == 0) ? $pengepul1 : $pengepul2;
            $qty = rand(50, 1000); // Varied Quantity
            $total = $product->harga * $qty;

            // Legacy: Create Dummy Wallet for Buyer
            $walletId = DB::table('e_wallets')->insertGetId([
                'saldo' => rand(100000000, 500000000),
                'id_user' => $buyer->id_user,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Transaksi::create([
                'id_penjual' => $product->id_petani,
                'id_pembeli' => $buyer->id_user,
                'id_produk' => $product->id_produk,
                'id_pasar' => $pasarId, 
                'id_wallet' => $walletId, 
                'jumlah' => $qty,
                'harga_awalan' => $product->harga,
                'harga_akhir' => $product->harga,
                'jenis_transaksi' => 'jual',
                'status_transaksi' => 'confirmed', // Correct Enum
                'tanggal' => $date,
                'type' => 'sale',
                'description' => 'Penjualan ' . $product->nama_produk,
                'user_id' => $buyer->id_user,
                'created_at' => $date, // Critical for ETL
                'updated_at' => $date // Critical for ETL
            ]);
        }

        // 4. Create Negotiations (Pending/Active)
        Negosiasi::create([
            'id_produk' => $products[0]->id_produk,
            'id_pengepul' => $pengepul1->id_user,
            'id_petani' => $products[0]->id_petani,
            'harga_awal' => $products[0]->harga,
            'harga_penawaran' => $products[0]->harga - 500,
            'jumlah_kg' => 1000,
            'status' => 'dalam_proses',
            'pesan' => 'Bisa kurang sedikit pak?'
        ]);

        // 5. Run ETL
        $this->command->info('Running ETL Aggregate...');
        \Illuminate\Support\Facades\Artisan::call('dashboard:aggregate');
        $this->command->info('Dashboard data populated!');
    }
}
