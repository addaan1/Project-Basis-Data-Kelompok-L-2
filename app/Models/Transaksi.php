<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    // Nama tabel sesuai database
    protected $table = 'transaksis';

    // Primary key custom (bukan 'id')
    protected $primaryKey = 'id_transaksi';

    // Karena kolom PK auto increment dan tipe integer
    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom yang bisa diisi mass-assignment
    protected $fillable = [
        'id_penjual',
        'id_pembeli',
        'id_pasar',
        'id_wallet',
        'jumlah',
        'harga_awalan',
        'harga_akhir',
        'tanggal',
        'jenis_transaksi',
        'status_transaksi',
        'type',
        'description',
        'payment_method',
        'reference_code',
        'user_id',
    ];

    protected $casts = [
        'payment_method' => 'encrypted',
        'reference_code' => 'encrypted',
    ];

    // Relasi ke User sebagai penjual
    public function penjual()
    {
        return $this->belongsTo(User::class, 'id_penjual', 'id_user');
    }

    // Relasi ke User sebagai pembeli
    public function pembeli()
    {
        return $this->belongsTo(User::class, 'id_pembeli', 'id_user');
    }

    // Relasi ke Pasar
    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'id_pasar', 'id_pasar');
    }

    // Relasi ke E-Wallet
    public function wallet()
    {
        return $this->belongsTo(EWallet::class, 'id_wallet', 'id_wallet');
    }
}