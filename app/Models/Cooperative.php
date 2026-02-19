<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cooperative extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nit',
        'phone',
        'email',
        'address',
        'legal_representative',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get all sales for this cooperative
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Check if cooperative is active
     */
    public function isActive(): bool
    {
        return $this->active === true;
    }

    /**
     * Check if cooperative is inactive
     */
    public function isInactive(): bool
    {
        return $this->active === false;
    }

    /**
     * Activate the cooperative
     */
    public function activate(): void
    {
        $this->update(['active' => true]);
    }

    /**
     * Deactivate the cooperative
     */
    public function deactivate(): void
    {
        $this->update(['active' => false]);
    }

    /**
     * Scope a query to only include active cooperatives
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive cooperatives
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }
}

