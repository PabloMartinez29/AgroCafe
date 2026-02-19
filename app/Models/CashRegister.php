<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'register_name',
        'initial_amount',
        'base_salary',
        'available_balance',
        'opening_date',
        'closing_date',
        'kilos_purchased',
        'kilos_sold',
        'operating_hours',
        'status',
    ];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'kilos_purchased' => 'decimal:2',
        'kilos_sold' => 'decimal:2',
        'operating_hours' => 'decimal:2',
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
    ];

    /**
     * Scope para cajas abiertas
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope para cajas cerradas
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Calcular tiempo de operación en formato legible
     */
    public function getOperatingTimeAttribute()
    {
        if ($this->status === 'open') {
            $openingTime = Carbon::parse($this->opening_date);
            $now = Carbon::now();
            $hours = $openingTime->diffInHours($now);
            $minutes = $openingTime->diffInMinutes($now) % 60;
            return "{$hours}h {$minutes}m";
        } else {
            $hours = floor($this->operating_hours);
            $minutes = round(($this->operating_hours - $hours) * 60);
            return "{$hours}h {$minutes}m";
        }
    }
}

