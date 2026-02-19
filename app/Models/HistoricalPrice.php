<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricalPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'coffee_type_id',
        'price',
        'price_date',
        'operation_type',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_date' => 'date',
    ];

    /**
     * Get the coffee type for this historical price
     */
    public function coffeeType(): BelongsTo
    {
        return $this->belongsTo(CoffeeType::class);
    }
}

