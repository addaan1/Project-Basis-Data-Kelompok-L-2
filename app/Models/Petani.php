<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Petani extends Model {
    use HasFactory;
    protected $fillable = ['nama', 'lokasi', 'kontak', 'kapasitas_panen', 'id_user'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}