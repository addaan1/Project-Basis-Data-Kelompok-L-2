<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ProdukBeras;
use App\Models\Negosiasi;
use Carbon\Carbon;

class NegosiasiSeeder extends Seeder
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
            $this->command->error('Users or Products data missing.');
            return;
        }

        $this->command->info('Generating 300 dummy negotiations for the last 365 days...');

        $totalNego = 300;
        $generated = 0;

        while ($generated < $totalNego) {
            $daysBack = rand(0, 365);
            $currentDate = Carbon::now()->subDays($daysBack);
            
            $pengepul = $pengepuls->random();
            $product = $products->random();
            $petani = User::find($product->id_petani);
            
            if (!$petani) continue;

            $qty = rand(500, 2000); 
            $awal = $product->harga;
            $tawar = $awal - rand(500, 1500); // Offer lower
            
            $statuses = ['Menunggu', 'disetujui', 'ditolak', 'ditolak', 'disetujui'];
            $status = $statuses[array_rand($statuses)];

            try {
                Negosiasi::create([
                    'id_petani' => $petani->id_user,
                    'id_pengepul' => $pengepul->id_user,
                    'id_produk' => $product->id_produk,
                    'harga_awal' => $awal,
                    'harga_penawaran' => $tawar,
                    'jumlah_kg' => $qty,
                    'pesan' => "Nego harga untuk {$qty} kg, boleh kurang?",
                    'status' => $status,
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ]);
                $generated++;
            } catch (\Exception $e) {
                // Ignore
            }
        }
        
        $this->command->info('Negotiations generation completed.');
    }
}
