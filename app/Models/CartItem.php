<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    Protected $fillable = ['cart_id', 'product_id', 'quantity', 'price', 'total', 'options'];

    Protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'options' => 'array'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getOptionAttributes($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    Protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total = $item->quantity * $item->price;
        });

        static::saved(function ($item) {
            $item->cart->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->cart->calculateTotals();
        });
    }
}
