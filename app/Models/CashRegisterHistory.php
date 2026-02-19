<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterHistory extends Model
{
    use HasFactory;

    protected $table = 'cash_register_history';

    protected $fillable = [
        'register_id',
        'opening_date',
        'closing_date',
        'available_kilos',
        'invested_value',
        'recovered_balance',
        'profits',
        'profit_margin',
        'kilos_sold',
        'kilos_purchased',
    ];

    protected $casts = [
        'available_kilos' => 'decimal:2',
        'invested_value' => 'decimal:2',
        'recovered_balance' => 'decimal:2',
        'profits' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'kilos_sold' => 'decimal:2',
        'kilos_purchased' => 'decimal:2',
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
    ];
}

