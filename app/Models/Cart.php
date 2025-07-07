<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotal()
    {
        return $this->quantity * $this->price;
    }

    public function getFormattedSubtotal()
    {
        return 'Rp ' . number_format($this->getSubtotal(), 0, ',', '.');
    }

    public function updateQuantity($quantity)
    {
        if ($this->product->isInStock($quantity)) {
            $this->quantity = $quantity;
            $this->save();
            return true;
        }
        return false;
    }
}