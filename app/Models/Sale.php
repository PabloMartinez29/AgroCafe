<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'cooperative_id',
        'client_name',
        'coffee_type_id',
        'quantity',
        'price_per_kg',
        'sale_date',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total' => 'decimal:2',
        'sale_date' => 'date',
    ];

    /**
     * Get the cooperative for this sale
     */
    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

    /**
     * Get the coffee type for this sale
     */
    public function coffeeType(): BelongsTo
    {
        return $this->belongsTo(CoffeeType::class);
    }

    /**
     * Get all payments for this sale
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the invoice for this sale
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}

