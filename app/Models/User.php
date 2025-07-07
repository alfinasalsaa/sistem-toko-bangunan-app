<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Helper methods for role checking
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    // Relationships (jika model lain sudah dibuat)
    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function approvedTransactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'approved_by');
    }

    public function getCartTotal()
    {
        return $this->carts->sum(function ($cart) {
            return $cart->quantity * $cart->price;
        });
    }

    public function getCartItemsCount()
    {
        return $this->carts->sum('quantity');
    }
}