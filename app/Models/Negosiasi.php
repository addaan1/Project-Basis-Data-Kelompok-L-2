<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Negosiasi extends Model
{
    use HasFactory;

    protected $table = 'negosiasis';

    protected $fillable = [
        'id_produk',
        'id_pengepul',
        'id_petani',
        'harga_penawaran',
        'harga_awal',
        'jumlah_kg',
        'pesan',
        'status',
    ];

    public function produk()
    {
        return $this->belongsTo(ProdukBeras::class, 'id_produk', 'id_produk');
    }

    public function petani()
    {
        return $this->belongsTo(User::class, 'id_petani', 'id_user');
    }

    public function pengepul()
    {
        return $this->belongsTo(User::class, 'id_pengepul', 'id_user');
    }
}