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

        $this->command->info('Generating 500 dummy transactions for the last 365 days...');

        $startDate = Carbon::now()->subDays(365);
        $totalTx = 500;
        $generated = 0;

        while ($generated < $totalTx) {
            $daysBack = rand(0, 365);
            $currentDate = Carbon::now()->subDays($daysBack);
            
            $buyer = $pengepuls->random();
            $product = $products->random();
            $seller = User::find($product->id_petani);
            
            if (!$seller) continue;

            $qty = rand(10, 1000); 
            $price = $product->harga;
            $dealPrice = $price - rand(0, 500); // Slight negotiation effect
            
            try {
                Transaksi::create([
                    'id_penjual' => $seller->id_user,
                    'id_pembeli' => $buyer->id_user,
                    'id_produk' => $product->id_produk,
                    'jumlah' => $qty,
                    'harga_awalan' => $price,
                    'harga_akhir' => $dealPrice,
                    'tanggal' => $currentDate->toDateString(),
                    'jenis_transaksi' => 'jual',
                    'status_transaksi' => 'disetujui', 
                    'created_at' => $currentDate->setTime(rand(8, 16), rand(0, 59)),
                    'updated_at' => $currentDate->setTime(rand(16, 20), rand(0, 59)),
                    'description' => "Penjualan {$qty} kg {$product->nama_produk}",
                    'user_id' => $buyer->id_user
                ]);
                $generated++;
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
        
        $this->command->info('Transactions generation completed.');
    }
}
