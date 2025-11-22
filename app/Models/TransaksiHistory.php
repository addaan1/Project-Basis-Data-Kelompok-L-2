<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiHistory extends Model
{
    use HasFactory;

    protected $table = 'transaksi_histories';

    protected $fillable = [
        'id_transaksi',
        'status_before',
        'status_after',
        'changed_by',
        'note',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by', 'id_user');
    }
}