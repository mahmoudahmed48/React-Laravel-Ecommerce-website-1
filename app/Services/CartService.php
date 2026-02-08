<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class CartService
{


    public function getCurrentCart(): cart
    {

        if (Auth::check())
        {
            $cart = Cart::firstOrCreate([
                ['user_id' => Auth::id()],
                ['sessio_id'=> Session::getId()] 
            ]);        
            
            $session_cart = $this->getSissionCart();

            if ($session_cart && $session_cart->id !== $cart->id)
            {
                $this->mergeCarts($session_cart, $cart);
            }
        }
        else
        {
            $cart = $this->getSessionCart();
        }

        return $cart;

    }

    Protected function getSessionCart(): cart 
    {
        $sessionId = Session::getId();

        return Cart::firstOrCreate([
            ['session_id' => $sessionId, 'user_id' => null],
            ['sessio_id' => $sessionId]
        ]);
    }

    Protected function mergeCarts(Cart $sourceCart, Cart $targetCart): void
    {
        foreach ($sourceCart->items as $sourceItem) 
        {
            $existingItem = $targetCart->items()->where('product_id', $sourceItem->product_id)->first();

            if ($existingItem)
            {
                $existingItem->quantity += $sourceItem->quantity;
                $existingItem->save();
            }
            else
            {
                $targetCart->items()->create([
                    'product_id' => $sourceItem->product_id,
                    'quantity' => $sourceItem->quantity,
                    'price' => $sourceItem->price,
                    'option' => $sourceItem->options,
                ]);
            }
        }

        $sourceCart->delete();
    }

    public function addToCart(int $productId, int $quantity = 1, array $options = [] ): CartItem
    {
        $cart = $this->getCurrentCart();
        $product = Product::findOrFail($productId);

        return $cart->addItem($product, $quantity, $options);
    }

    public function updateQuantity(int $productId, int $quantity): ?CartItem 
    {
        $cart = $this->getCurrentCart();
        return $cart->updateItemQuantity($productId, $quantity);
    }

    public function removeFromCart(int $productId): bool 
    {
        $cart = $this->getCurrentCart();
        return $cart->removeItem($productId);
    }

    public function clearCart(): void 
    {
        $cart = $this->getCurrentCart();
        $cart->clear();
    }

    public function getCartSummary(): array
    {
        $cart = $this->getCurrentCart();
        return $cart->getSummary();
    }

    public function getCartItemsCount(): int 
    {
        $cart = $this->getCurrentCart();
        return $cart->getItemsCount();
    }

    public function applyCoupon(string $code): array 
    {
        $cart = $this->getCurrentCart();

        $discount = 0;

        if ($code === 'WELCOME10X' )
        {
            $discount = $cart->subtotal * 0.10;
        }
        else if ($code === 'FREESHIPPING')
        {
            $discount = $cart->shipping;
        }

        $cart->discount = $discount;
        $cart->coupon = ['code' => $code, 'discount' => $discount];
        $cart->calculateTotals();

        return [
            'success' => true,
            'message' => 'Coupon Applied Successfully!',
            'discount' => $discount
        ];

    }

    public function removeCoupon():void 
    {
        $cart = $this->getCurrentCart();
        $cart->discount = 0;
        $cart->coupon = null;
        $cart->calculateTotals();
    }

    public function convertToOrder(): array 
    {
        $cart = $this->getCurrentCart();

        if ($cart->items->isEmpty())
        {
            throw new \Exception('Cart Is Empty!');
        }

        return [
            'cart' => $cart,
            'items_count' => $cart->getItemsCount(),
            'total' => $cart->total
        ];
    }


}