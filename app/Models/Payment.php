<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'amount',
        'payment_method',
        'reference',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the sale for this payment
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}

