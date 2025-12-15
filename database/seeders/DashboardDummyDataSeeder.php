<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardDummyDataSeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Database\Eloquent\Model::unguard();
        
        $users = \App\Models\User::all();
        $startDate = now()->subDays(365); // 1 Year back

        // Pastikan ada Pasar, kalau tidak buat dummy
        $pasar = \App\Models\Pasar::first() ?? \App\Models\Pasar::create([
            'nama_produk' => 'Beras Dummy',
            'harga_tertinggi' => 15000,
            'harga_terendah' => 12000,
            'permintaan' => 100,
            'ketersediaan' => 100,
        ]);

        foreach ($users as $user) {
            // Skip admin from trading
            if ($user->peran == 'admin') continue;

            // Ensure E-Wallet exists
            $wallet = \App\Models\EWallet::firstOrCreate(
                ['id_user' => $user->id_user],
                ['saldo' => 10000000] // Default balance
            );

            $this->command->info("Seeding transactions for user: {$user->name} ({$user->peran})");

            for ($i = 0; $i < 366; $i++) {
                $date = $startDate->copy()->addDays($i);
                
                // Random chance to have transaction on this day (Increased for density)
                if (rand(0, 10) > 2) { // 80% chance of activty
                    // Potentially multiple transactions per day
                    $dailyTxCount = rand(1, 3);
                    
                    for ($j = 0; $j < $dailyTxCount; $j++) {
                        $isSelling = rand(0, 1) == 1; // 50/50 Chance selling or buying
                        
                        $qty = rand(50, 1000); // 50-1000 kg (Larger range)
                        $price = rand(12000, 14000);
                        
                        // Add some time variation within the day
                        $txTime = $date->copy()->addHours(rand(6, 20))->addMinutes(rand(0, 59));

                        // Variasi Status Transaksi
                        // 80% Confirmed, 10% Pending, 10% Cancelled
                        $statusRoll = rand(1, 100);
                        if ($statusRoll <= 80) $status = 'confirmed';
                        elseif ($statusRoll <= 90) $status = 'pending';
                        else $status = 'cancelled';

                        // Buat transaksi
                        \App\Models\Transaksi::create([
                            'id_penjual' => $isSelling ? $user->id_user : ($users->where('id_user', '!=', $user->id_user)->random()->id_user ?? 1),
                            'id_pembeli' => $isSelling ? ($users->where('id_user', '!=', $user->id_user)->random()->id_user ?? 2) : $user->id_user,
                            'id_produk' => 3, // Asumsi 3 = Beras, dummy
                            'id_pasar' => $pasar->id_pasar,
                            'id_wallet' => $wallet->id_wallet, // REQUIRED
                            'jumlah' => $qty,
                            'harga_awalan' => $price,
                            'harga_akhir' => $price,
                            'tanggal' => $txTime->toDateString(),
                            'jenis_transaksi' => $isSelling ? 'jual' : 'beli',
                            'status_transaksi' => $status, 
                            'type' => $isSelling ? 'sale' : 'purchase',
                            'created_at' => $txTime,
                            'updated_at' => $txTime
                        ]);
                    }
                }
            }

            // --- Seed Negotiations for this user ---
            // Only if user is Petani or Pengepul
            if (in_array($user->peran, ['petani', 'pengepul'])) {
                $negCount = rand(5, 15);
                $this->command->info("  - Seeding $negCount negotiations...");
                
                for ($k = 0; $k < $negCount; $k++) {
                    $negDate = now()->subDays(rand(0, 30)); // Recent negotiations
                    
                    $targetPeran = $user->peran == 'petani' ? 'pengepul' : 'petani';
                    $targetUser = $users->where('peran', $targetPeran)->random();

                    if (!$targetUser) continue;

                    // Status distribution
                    $sRoll = rand(1, 100);
                    if ($sRoll <= 40) $nStatus = 'Menunggu';
                    elseif ($sRoll <= 70) $nStatus = 'Disetujui';
                    else $nStatus = 'Ditolak';

                    \App\Models\Negosiasi::create([
                        'id_produk' => 3, 
                        'id_pengepul' => $user->peran == 'pengepul' ? $user->id_user : $targetUser->id_user,
                        'id_petani' => $user->peran == 'petani' ? $user->id_user : $targetUser->id_user,
                        'harga_penawaran' => rand(12500, 13500),
                        'harga_awal' => 14000,
                        'jumlah_kg' => rand(100, 1000),
                        'pesan' => 'Nego harga bos',
                        'status' => $nStatus,
                        'created_at' => $negDate,
                        'updated_at' => $negDate
                    ]);
                }
            }
        }
    }
}
