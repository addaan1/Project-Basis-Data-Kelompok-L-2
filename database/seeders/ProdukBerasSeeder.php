<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProdukBeras;
use App\Models\User;

class ProdukBerasSeeder extends Seeder
{
    public function run(): void
    {
        ProdukBeras::create([
            'nama_produk'   => 'Beras IR64',
            'jenis_beras'   => 'IR64',
            'harga'         => 12000,
            'nama_petani'   => 'Pak Budi',
            'lokasi_gudang' => 'Lamongan',
            'stok'          => 1000,
            'deskripsi'     => 'Beras putih kualitas medium',
            'foto'          => null,
            'id_user'       => User::where('peran', 'petani')->first()->id_user ?? 1,
        ]);

        ProdukBeras::create([
            'nama_produk'   => 'Beras Pandan Wangi',
            'jenis_beras'   => 'Pandan Wangi',
            'harga'         => 15000,
            'nama_petani'   => 'Bu Siti',
            'lokasi_gudang' => 'Cianjur',
            'stok'          => 500,
            'deskripsi'     => 'Beras wangi khas Cianjur, pulen dan harum',
            'foto'          => null,
            'id_user'       => User::where('peran', 'petani')->first()->id_user ?? 1,
        ]);

        ProdukBeras::create([
            'nama_produk'   => 'Beras Merah',
            'jenis_beras'   => 'Merah',
            'harga'         => 18000,
            'nama_petani'   => 'Pak Joko',
            'lokasi_gudang' => 'Banyuwangi',
            'stok'          => 300,
            'deskripsi'     => 'Beras merah sehat, cocok untuk diet',
            'foto'          => null,
            'id_user'       => User::where('peran', 'petani')->first()->id_user ?? 1,
        ]);
    }
}