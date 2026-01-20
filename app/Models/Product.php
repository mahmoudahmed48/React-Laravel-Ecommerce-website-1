<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    //

    use HasFactory;

    Protected $fillable = ['name', 'slug', 'description', 'price', 'compare_price', 'quantity', 'sku', 'image', 'images', 'category_id', 'featured', 'status'];

    Protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2', 
        'compare_price' => 'decimal:2',
        'featured' => 'boolean',
        'status' => 'boolean'
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')->withPivot('quantity', 'price', 'total')->withTimestamps();
    }
}
