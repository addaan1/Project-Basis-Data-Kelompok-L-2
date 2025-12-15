<?php

namespace Database\Seeders;

use App\Models\ProdukBeras;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Petani ID is 2 (from UserSeeder)
        // Petani ID (Get dynamically)
        $petani = \App\Models\User::where('peran', 'petani')->first();
        $petaniId = $petani ? $petani->id_user : 1;

        ProdukBeras::create([
            'nama_produk' => 'Beras Pandan Wangi',
            'jenis_beras' => 'Pandan Wangi',
            'kualitas' => 'Premium',
            'harga' => 15000,
            'stok' => 100,
            'nama_petani' => 'Pak Budi Petani',
            'id_petani' => $petaniId,
            'lokasi_gudang' => 'Gudang Cianjur',
            'deskripsi' => 'Beras Pandan Wangi asli Cianjur, wangi dan pulen.',
            'foto' => null,
        ]);

        ProdukBeras::create([
            'nama_produk' => 'Beras IR 64',
            'jenis_beras' => 'IR 64',
            'kualitas' => 'Medium',
            'harga' => 12000,
            'stok' => 500,
            'nama_petani' => 'Pak Budi Petani',
            'id_petani' => $petaniId,
            'lokasi_gudang' => 'Gudang Karawang',
            'deskripsi' => 'Beras IR 64 kualitas medium, cocok untuk nasi goreng.',
            'foto' => null,
        ]);

        ProdukBeras::create([
            'nama_produk' => 'Beras Merah Organik',
            'jenis_beras' => 'Merah',
            'kualitas' => 'Premium',
            'harga' => 20000,
            'stok' => 50,
            'nama_petani' => 'Pak Budi Petani',
            'id_petani' => $petaniId,
            'lokasi_gudang' => 'Gudang Jogja',
            'deskripsi' => 'Beras merah organik sehat, rendah gula.',
            'foto' => null,
        ]);
    }
}
