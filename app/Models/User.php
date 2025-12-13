<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
    use HasFactory, Notifiable;
    protected $primaryKey = 'id_user';
    protected $fillable = ['nama', 'email', 'password', 'peran', 'saldo', 'bank_name', 'account_number', 'account_name'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    // Relasi ke Profile Petani
    public function petani()
    {
        return $this->hasOne(Petani::class, 'id_user');
    }

    // Relasi ke Profile Pengepul
    public function pengepul()
    {
        return $this->hasOne(Pengepul::class, 'id_user');
    }

    // Relasi ke Produk (Jika User adalah Petani/Penjual)
    public function products()
    {
        return $this->hasMany(ProdukBeras::class, 'id_petani', 'id_user'); // Asumsi id_petani di tabel produk merujuk ke id_user atau id_petani? Perlu dicek.
    }

    // Relasi Transaksi sebagai Penjual
    public function penjualan()
    {
        return $this->hasMany(Transaksi::class, 'id_penjual');
    }

    // Relasi Transaksi sebagai Pembeli
    public function pembelian()
    {
        return $this->hasMany(Transaksi::class, 'id_pembeli');
    }
}