<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Carbon\Carbon;

class DwhSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->command->info('Starting Robust Seeder...');

        // --- PART 1: DWH (Wrapped in Try-Catch) ---
        try {
            $this->command->info('Truncating DWH Tables...');
            DB::table('fact_transaksi')->truncate();
            DB::table('fact_negosiasi')->truncate();
            DB::table('fact_stok_snapshot')->truncate();
            DB::table('fact_user_daily_metrics')->truncate();
            DB::table('dim_waktu')->truncate();
            DB::table('dim_users')->truncate();
            DB::table('dim_produk')->truncate();

            // 1. Dim Waktu
            $startDate = Carbon::now()->subMonths(6)->startOfMonth();
            $endDate = Carbon::now();
            $current = $startDate->copy();
            while ($current <= $endDate) {
                DB::table('dim_waktu')->insert([
                    'id_waktu' => (int) $current->format('Ymd'),
                    'tanggal' => $current->format('Y-m-d'),
                    'hari' => $current->day,
                    'bulan' => $current->month,
                    'nama_bulan' => $current->translatedFormat('F'),
                    'tahun' => $current->year,
                    'kuartal' => $current->quarter,
                    'nama_hari' => $current->translatedFormat('l'),
                    'is_akhir_pekan' => $current->isWeekend(),
                ]);
                $current->addDay();
            }

            // 2. Dim Users
            $users = User::all();
            foreach ($users as $user) {
                DB::table('dim_users')->insert([
                    'id_user_asli' => $user->id_user,
                    'nama' => $user->nama,
                    'peran' => $user->peran,
                    'lokasi' => 'Indonesia',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 3. Dim Produk
            $products = DB::table('produk_beras')->get(); 
            foreach ($products as $product) {
                DB::table('dim_produk')->insert([
                    'id_produk_asli' => $product->id_produk,
                    'nama_produk' => $product->nama_produk,
                    'jenis_beras' => $product->jenis_beras,
                    'kualitas' => $product->kualitas,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $petani = DB::table('dim_users')->where('peran', 'petani')->first();
            $pengepul = DB::table('dim_users')->where('peran', 'pengepul')->first();
            $produk = DB::table('dim_produk')->first();

            if ($petani && $produk) {
                // 4. Fact Transaksi
                for ($i = 0; $i < 50; $i++) {
                    $date = Carbon::now()->subDays(rand(1, 180));
                    $sk_waktu = (int) $date->format('Ymd');
                    DB::table('fact_transaksi')->insert([
                        'sk_penjual' => $petani->sk_user,
                        'sk_pembeli' => $pengepul ? $pengepul->sk_user : 0,
                        'sk_produk' => $produk ? $produk->sk_produk : 0,
                        'sk_waktu' => $sk_waktu,
                        'no_transaksi_asli' => 'TRX-' . rand(10000, 99999),
                        'jumlah_kg' => rand(100, 1000),
                        'nilai_transaksi' => rand(1000000, 10000000),
                        'harga_per_kg' => rand(9000, 15000),
                        'jenis_transaksi' => 'jual',
                        'status_transaksi' => 'completed',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 5. Fact Stok
                for ($i = 0; $i < 7; $i++) {
                    $date = Carbon::now()->subDays($i);
                    $sk_waktu = (int) $date->format('Ymd');
                    DB::table('fact_stok_snapshot')->insert([
                        'sk_produk' => $produk ? $produk->sk_produk : 0,
                        'sk_pemilik' => $petani->sk_user,
                        'sk_waktu' => $sk_waktu,
                        'stok_akhir_hari' => rand(500, 2000),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // 6. Fact Negosiasi
                $statuses = ['Menunggu', 'Disetujui', 'Ditolak'];
                for ($i = 0; $i < 20; $i++) {
                    $date = Carbon::now()->subDays(rand(1, 30));
                    $sk_waktu = (int) $date->format('Ymd');
                    DB::table('fact_negosiasi')->insert([
                        'sk_pengaju' => $pengepul ? $pengepul->sk_user : 0,
                        'sk_penerima' => $petani->sk_user,
                        'sk_produk' => $produk ? $produk->sk_produk : 0,
                        'sk_waktu' => $sk_waktu,
                        // id_nego_asli removed
                        'harga_tawaran' => rand(9000, 12000),
                        'harga_deal' => rand(9000, 12000),
                        'status_akhir' => $statuses[array_rand($statuses)],
                        'selisih_harga' => rand(-500, 500),
                        'durasi_negosiasi_jam' => rand(1, 48),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $this->command->info('DWH Population Success.');

        } catch (\Throwable $e) {
            $this->command->error('DWH Population Failed (Skipping to Live Data): ' . $e->getMessage());
        }

        // --- PART 2: LIVE DATA (ALWAYS RUN) ---
        $this->command->info('Starting LIVE DATA Injection...');
        $livePetani = User::where('email', 'petani@warungpadi.com')->first();
        
        if ($livePetani) {
            // A. Fact User Daily Metrics (Hybrid - moved here to ensure display)
            // Even if DWH failed, we force this aggregate for Top Cards
            try {
                DB::table('fact_user_daily_metrics')->truncate(); // Retry truncate
                DB::table('fact_user_daily_metrics')->insert([
                    'date' => Carbon::yesterday()->toDateString(),
                    'user_id' => $livePetani->id_user,
                    'role' => $livePetani->peran,
                    'total_income' => 88000000.00,
                    'total_expense' => 12000000.00,
                    'total_kg_sold' => 8500.00,
                    'total_kg_bought' => 0.00,
                    'transaction_count' => 25,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info('User Daily Metrics updated.');
            } catch (\Throwable $e) { $this->command->error('Metrics Failed: '.$e->getMessage()); }

            // B. Inventory
            DB::table('inventories')->where('id_user', $livePetani->id_user)->delete();
            DB::table('inventories')->insert([
                'id_user' => $livePetani->id_user,
                'jenis_beras' => 'Pandan Wangi',
                'kualitas' => 'Premium',
                'jumlah' => 5000,
                'tanggal_masuk' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'keterangan' => 'Seeded Data'
            ]);
             DB::table('inventories')->insert([
                'id_user' => $livePetani->id_user,
                'jenis_beras' => 'IR 64',
                'kualitas' => 'Medium',
                'jumlah' => 2000,
                'tanggal_masuk' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'keterangan' => 'Seeded Data'
            ]);

            // C. Negosiasi
            DB::table('negosiasis')->insert([
                'id_petani' => $livePetani->id_user,
                'id_pengepul' => 2, // fallback ID
                'id_produk' => 1,
                'harga_penawaran' => 11000,
                'jumlah_kg' => 1500,
                'status' => 'Menunggu',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // D. Transaksi
            DB::table('transaksis')->insert([
                'id_penjual' => $livePetani->id_user,
                'id_pembeli' => 2,
                'id_produk' => 1,
                'jumlah' => 300,
                'harga_awalan' => 12000,
                'harga_akhir' => 12000,
                'tanggal' => now(),
                'jenis_transaksi' => 'jual',
                'status_transaksi' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->command->info('Live Data Injection Complete.');
        }

        Schema::enableForeignKeyConstraints();
    }
}
