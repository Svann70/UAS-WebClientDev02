<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     * `role` sengaja TIDAK dimasukkan: peran hanya ditentukan sistem,
     * bukan dari input form (mencegah privilege escalation / mass assignment).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============ Relasi ============

    /** Merchant memiliki satu profil toko. */
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    /** Customer memiliki banyak pesanan. */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ============ Helper Role ============

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMerchant(): bool
    {
        return $this->role === 'merchant';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}