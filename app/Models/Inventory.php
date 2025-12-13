<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Inventory extends Model {
    use HasFactory;
    protected $primaryKey = 'id_inventory';
    protected $fillable = [
        'jumlah', 
        'tanggal_masuk', 
        'tanggal_keluar', 
        'id_user',
        'jenis_beras',
        'kualitas',
        'keterangan'
    ];
    public function user() { return $this->belongsTo(User::class, 'id_user', 'id_user'); }
}