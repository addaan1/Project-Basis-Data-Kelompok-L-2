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
        Schema::connection('mysql_dashboard')->disableForeignKeyConstraints();

        $this->command->info('Starting Robust Seeder...');

        // --- PART 1: DWH (Wrapped in Try-Catch) ---
        try {
            $this->command->info('Truncating DWH Tables...');
            DB::connection('mysql_dashboard')->table('fact_transaksi')->truncate();
            DB::connection('mysql_dashboard')->table('fact_negosiasi')->truncate();
            DB::connection('mysql_dashboard')->table('fact_stok_snapshot')->truncate();
            DB::connection('mysql_dashboard')->table('fact_user_daily_metrics')->truncate();
            DB::connection('mysql_dashboard')->table('dim_waktu')->truncate();
            DB::connection('mysql_dashboard')->table('dim_users')->truncate();
            DB::connection('mysql_dashboard')->table('dim_produk')->truncate();

            // 1. Dim Waktu
            $startDate = Carbon::now()->subMonths(6)->startOfMonth();
            $endDate = Carbon::now();
            $current = $startDate->copy();
            while ($current <= $endDate) {
                DB::connection('mysql_dashboard')->table('dim_waktu')->insert([
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
                DB::connection('mysql_dashboard')->table('dim_users')->insert([
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
                DB::connection('mysql_dashboard')->table('dim_produk')->insert([
                    'id_produk_asli' => $product->id_produk,
                    'nama_produk' => $product->nama_produk,
                    'jenis_beras' => $product->jenis_beras,
                    'kualitas' => $product->kualitas,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $petaniIds = DB::connection('mysql_dashboard')->table('dim_users')->where('peran', 'petani')->pluck('sk_user')->toArray();
            $pengepulIds = DB::connection('mysql_dashboard')->table('dim_users')->where('peran', 'pengepul')->pluck('sk_user')->toArray();
            $produkIds = DB::connection('mysql_dashboard')->table('dim_produk')->pluck('sk_produk')->toArray();

            if (!empty($petaniIds) && !empty($produkIds)) {
                
                // 4A. GLOBAL RANDOM TRANSACTIONS (Background Noise)
                for ($i = 0; $i < 300; $i++) {
                    $date = Carbon::now()->subDays(rand(1, 360));
                    $sk_waktu = (int) $date->format('Ymd');
                    
                    DB::connection('mysql_dashboard')->table('fact_transaksi')->insert([
                        'sk_penjual' => $petaniIds[array_rand($petaniIds)],
                        'sk_pembeli' => !empty($pengepulIds) ? $pengepulIds[array_rand($pengepulIds)] : 0,
                        'sk_produk' => $produkIds[array_rand($produkIds)],
                        'sk_waktu' => $sk_waktu,
                        'no_transaksi_asli' => 'TRX-RND-' . rand(10000, 99999),
                        'jumlah_kg' => rand(100, 1000),
                        'nilai_transaksi' => rand(1000000, 10000000),
                        'harga_per_kg' => rand(9000, 15000),
                        'jenis_transaksi' => 'jual',
                        'status_transaksi' => 'completed',
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }

                // 4B. TARGETED TRANSACTIONS FOR MAIN "PETANI" (Guarantee 500 Records)
                // We pick the first Petani (usually seed user) to flood with data
                $mainPetaniSk = $petaniIds[0]; 
                
                for ($i = 0; $i < 500; $i++) {
                    $date = Carbon::now()->subDays(rand(1, 360));
                    $sk_waktu = (int) $date->format('Ymd');
                    
                    DB::connection('mysql_dashboard')->table('fact_transaksi')->insert([
                        'sk_penjual' => $mainPetaniSk, // THIS IS THE TARGET
                        'sk_pembeli' => !empty($pengepulIds) ? $pengepulIds[array_rand($pengepulIds)] : 0,
                        'sk_produk' => $produkIds[array_rand($produkIds)],
                        'sk_waktu' => $sk_waktu,
                        'no_transaksi_asli' => 'TRX-PET-' . rand(10000, 99999),
                        'jumlah_kg' => rand(500, 2000), // Larger volumes for visibility
                        'nilai_transaksi' => rand(5000000, 20000000), 
                        'harga_per_kg' => rand(10000, 16000),
                        'jenis_transaksi' => 'jual',
                        'status_transaksi' => 'completed',
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }

                // 5. Fact Stok (Last 30 Days) - Ensure all users have stock history
                $allUsers = array_merge($petaniIds, $pengepulIds);
                foreach ($allUsers as $userId) {
                    for ($i = 0; $i < 30; $i++) {
                        $date = Carbon::now()->subDays($i);
                        $sk_waktu = (int) $date->format('Ymd');
                        
                        DB::connection('mysql_dashboard')->table('fact_stok_snapshot')->insert([
                            'sk_produk' => $produkIds[array_rand($produkIds)],
                            'sk_pemilik' => $userId, 
                            'sk_waktu' => $sk_waktu,
                            'stok_akhir_hari' => rand(500, 5000),
                            'stok_masuk_hari_ini' => rand(0, 500),
                            'stok_keluar_hari_ini' => rand(0, 500),
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);
                    }
                }
                
                // 6. Fact Negosiasi (300 Data)
                $statuses = ['Menunggu', 'Disetujui', 'Ditolak', 'Ditolak', 'Disetujui'];
                for ($i = 0; $i < 300; $i++) {
                    $date = Carbon::now()->subDays(rand(1, 180));
                    $sk_waktu = (int) $date->format('Ymd');
                    $basePrice = rand(10000, 14000);
                    $offerPrice = $basePrice - rand(500, 2000);
                    $dealPrice = $offerPrice + rand(0, 1000);
                    
                    $sellerId = $petaniIds[array_rand($petaniIds)];
                    $buyerId = !empty($pengepulIds) ? $pengepulIds[array_rand($pengepulIds)] : 0;

                    DB::connection('mysql_dashboard')->table('fact_negosiasi')->insert([
                        'sk_pengaju' => $buyerId,
                        'sk_penerima' => $sellerId,
                        'sk_produk' => $produkIds[array_rand($produkIds)],
                        'sk_waktu' => $sk_waktu,
                        'harga_tawaran' => $offerPrice,
                        'harga_deal' => $dealPrice,
                        'status_akhir' => $statuses[array_rand($statuses)],
                        'selisih_harga' => $dealPrice - $offerPrice,
                        'durasi_negosiasi_jam' => rand(1, 72),
                        'created_at' => $date,
                        'updated_at' => $date,
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
                DB::connection('mysql_dashboard')->table('fact_user_daily_metrics')->truncate(); // Retry truncate
                DB::connection('mysql_dashboard')->table('fact_user_daily_metrics')->insert([
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
        Schema::connection('mysql_dashboard')->enableForeignKeyConstraints();
    }
}
