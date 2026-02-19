<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoffeeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'variety',
        'description',
        'base_price',
        'quality',
        'processing_type',
        'active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'active' => 'boolean',
    ];

    protected $appends = ['available_quantity'];

    /**
     * Get all purchases for this coffee type
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get all sales for this coffee type
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all historical prices for this coffee type
     */
    public function historicalPrices(): HasMany
    {
        return $this->hasMany(HistoricalPrice::class);
    }

    /**
     * Get all inventory movements for this coffee type
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Check if coffee type is active
     */
    public function isActive(): bool
    {
        return $this->active === true;
    }

    /**
     * Check if coffee type is inactive
     */
    public function isInactive(): bool
    {
        return $this->active === false;
    }

    /**
     * Activate the coffee type
     */
    public function activate(): void
    {
        $this->update(['active' => true]);
    }

    /**
     * Deactivate the coffee type
     */
    public function deactivate(): void
    {
        $this->update(['active' => false]);
    }

    /**
     * Scope a query to only include active coffee types
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive coffee types
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    /**
     * Get the Spanish translation of the processing type
     */
    public function getProcessingTypeSpanishAttribute(): string
    {
        $translations = [
            'normal' => 'Normal',
            'wet' => 'Mojado',
            'dry' => 'Seco',
            'pasilla' => 'Pasilla',
        ];

        return $translations[$this->processing_type] ?? ucfirst($this->processing_type);
    }

    /**
     * Static method to translate processing type
     */
    public static function translateProcessingType(string $type): string
    {
        $translations = [
            'normal' => 'Normal',
            'wet' => 'Mojado',
            'dry' => 'Seco',
            'pasilla' => 'Pasilla',
        ];

        return $translations[strtolower($type)] ?? ucfirst($type);
    }

    /**
     * Get available quantity attribute
     */
    public function getAvailableQuantityAttribute()
    {
        $purchased = $this->purchases()
            ->where('status', 'completed')
            ->sum('quantity');

        $sold = $this->sales()
            ->where('status', 'completed')
            ->sum('quantity');

        $adjustments = $this->inventoryMovements()
            ->where('movement_type', 'adjustment')
            ->sum('quantity');

        $entries = $this->inventoryMovements()
            ->where('movement_type', 'entry')
            ->sum('quantity');

        $exits = $this->inventoryMovements()
            ->where('movement_type', 'exit')
            ->sum('quantity');

        $returns = $this->inventoryMovements()
            ->where('movement_type', 'return')
            ->sum('quantity');

        $available = $purchased - $sold + $adjustments + $entries - $exits + $returns;
        
        return max(0, $available);
    }
}

