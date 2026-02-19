<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'peasant_id',
        'coffee_type_id',
        'quantity',
        'price_per_kg',
        'purchase_date',
        'status',
        'observations',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    /**
     * Get the peasant (user) who made this purchase
     */
    public function peasant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peasant_id');
    }

    /**
     * Get the coffee type for this purchase
     */
    public function coffeeType(): BelongsTo
    {
        return $this->belongsTo(CoffeeType::class);
    }

    /**
     * Get all payments for this purchase
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PeasantPayment::class);
    }

    /**
     * Get the invoice for this purchase
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the total price of the purchase
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->price_per_kg;
    }
}

