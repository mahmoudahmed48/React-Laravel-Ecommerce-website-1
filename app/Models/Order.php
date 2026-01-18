<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    //
    use HasFactory;

    Protected $fillable = [ 'order_number', 'user_id', 'status', 'total', 'shipping_cost', 'tax', 'discount', 'grand_total', 'payment_method', 'payment_status', 'notes', 'shipping_address', 'billing_address'];

    Protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array', 
        'total' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')->withPivot('quantity', 'price', 'total')->withTimeStamps();
    }
}
