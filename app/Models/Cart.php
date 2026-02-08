<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    Protected $fillable = ['user_id', 'session_id', 'subtotal', 'tax', 'shipping', 'discount', 'total', 'coupon'];

    Protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'coupon' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    } 

    public function calculatedTotals(): void 
    {
        $this->subtotal = $this->items->sum('total');

        $this->tax = $this->subtotal * 0.10;

        $this->shipping = $this->subtotal > 100 ? 0 : 10;

        $this->total = $this->subtotal + $this->tax + $this->shipping - $this->discount;

        $this->save();

    }

    public function addItem(Product $product, int $quantity = 1 , array $options = [] ): cartItem 
    {

        if ($product->quantity < $quantity)
        {
            throw new \Exception('The Required Quantity Not Available');
        }

        $existingItem = $this->items()->where('product_id', $product->id)->first();

        if ($existingItem)
        {
            $existingItem->quantity += $quantity;
            $existingItem->total = $existingItem->$quantity * $existingItem->$price;
            $existingItem->save();
            return $existingItem; 
        }
        else 
        {
            $item = $this->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'total' => $product->price * $quantity,
                'options' => $options
            ]);
        }

        return $item;

    }

    public function updateItemQuantity(int $productId, int $quantity): ?CartItem
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if(!$item)
        {
            return null;
        }

        $product = Product::find($productId);
        
        if ($product->quantity < $quantity)
        {
            throw new \Exception('The Quantity Not Available');
        }

        $item->quantity = $quantity;
        $item->total = $item->quantity * $item->price;
        $item->save();
        return $item;
    }
    
    public function removeItem(int $productId): bool 
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item)
        {
            $item->delete();
            return true;
        }

        return false;
    }

    public function clear(): void 
    {
        $this->items()->delete();
        $this->calculatedTotals();
    }

    public function getItemsCount(): int 
    {
        return $this->items->sum('quantity');
    }

    public function getSummary(): array
    {
        return [
            'items_count' => $this->getItemsCount(),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'shipping' => $this->shipping,
            'discount' => $this->discount,
            'total' => $this->total,
            'items' => $this->items->load('product')

        ];
    }

}
