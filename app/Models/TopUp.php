<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'reference_code',
        'status',
        'payment_method',
        'bukti_transfer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}