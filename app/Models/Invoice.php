<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'purchase_id',
        'invoice_number',
        'invoice_date',
        'subtotal',
        'taxes',
        'total',
        'payment_status',
        'due_date',
        'transaction_type',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'taxes' => 'decimal:2',
        'total' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the sale for this invoice
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the purchase for this invoice
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}

