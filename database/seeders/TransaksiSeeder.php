<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ProdukBeras;
use App\Models\Transaksi;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petanis = User::where('peran', 'petani')->get();
        $pengepuls = User::where('peran', 'pengepul')->get();
        $products = ProdukBeras::all();
        
        if ($petanis->isEmpty() || $pengepuls->isEmpty() || $products->isEmpty()) {
            $this->command->error('Users or Products data missing. Please run UserSeeder and ProductSeeder first.');
            return;
        }

        $this->command->info('Generating dummy transactions for the last 30 days...');

        $startDate = Carbon::now()->subDays(30);

        for ($i = 0; $i <= 30; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dailyTxCount = rand(2, 5); // 2-5 transactions per day

            for ($j = 0; $j < $dailyTxCount; $j++) {
                $buyer = $pengepuls->random();
                $product = $products->random();
                // Ensure seller is the product owner
                $seller = User::find($product->id_petani); 
                
                if (!$seller) continue;

                $qty = rand(10, 100); // 10-100 kg
                $price = $product->harga;
                
                try {
                    Transaksi::create([
                        'id_penjual' => $seller->id_user,
                        'id_pembeli' => $buyer->id_user,
                        'id_produk' => $product->id_produk,
                        'jumlah' => $qty,
                        'harga_awalan' => $price,
                        'harga_akhir' => $price, // Deal price
                        'tanggal' => $currentDate->toDateString(),
                        'jenis_transaksi' => 'jual',
                        'status_transaksi' => 'disetujui', // Aggregated data usually from completed tx
                        'created_at' => $currentDate->setTime(rand(8, 16), rand(0, 59)),
                        'updated_at' => $currentDate->setTime(rand(16, 20), rand(0, 59)), // Completed later that day
                        'description' => "Transaksi dummy #{$i}-{$j}",
                        'user_id' => $buyer->id_user
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Failed to insert transaction: " . $e->getMessage());
                }
            }
        }
        
        $this->command->info('Transactions generation completed.');
    }
}
