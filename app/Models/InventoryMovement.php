<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'coffee_type_id',
        'quantity',
        'movement_type',
        'reason',
        'user_id',
        'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    /**
     * Get the coffee type for this movement
     */
    public function coffeeType(): BelongsTo
    {
        return $this->belongsTo(CoffeeType::class);
    }

    /**
     * Get the user who made this movement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

