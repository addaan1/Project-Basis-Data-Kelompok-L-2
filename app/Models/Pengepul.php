<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Pengepul extends Model {
    use HasFactory;
    protected $fillable = ['nama', 'lokasi', 'kapasitas_tampung', 'id_user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}