<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactUserDailyMetric extends Model
{
    use HasFactory;

    protected $connection = 'mysql_dashboard';
    protected $table = 'fact_user_daily_metrics';
    
    protected $fillable = [
        'date',
        'user_id',
        'role',
        'total_income',
        'total_expense',
        'total_kg_sold',
        'total_kg_bought',
        'transaction_count'
    ];
    
    protected $casts = [
        'date' => 'date',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
    ];
}
