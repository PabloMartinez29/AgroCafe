<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'active',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    /**
     * Get all purchases made by this peasant
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'peasant_id');
    }

    /**
     * Get all inventory movements made by this user
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is peasant
     */
    public function isPeasant(): bool
    {
        return $this->role === 'peasant';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->active === true;
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->active === false;
    }

    /**
     * Activate the user
     */
    public function activate(): void
    {
        $this->update(['active' => true]);
    }

    /**
     * Deactivate the user
     */
    public function deactivate(): void
    {
        $this->update(['active' => false]);
    }

    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    /**
     * Enviar la notificación de restablecimiento de contraseña.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
