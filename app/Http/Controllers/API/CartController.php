<?php

namespace App\Http\Controllers\API;

use CartService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    Protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        try
        {
            $summary = $this->cartService->getCartSummary();
            return $this->success($summary);
        }
        catch(\Exception $e)
        {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'options' => 'nullable|array',
            'options.color' => 'nullable|string',
            'options.size'  => 'nullable|string'
        ]);

        if ($validator->fails())
        {
            return $this->validationError($validator->errors());
        }

        try
        {
            $item = $this->cartService->addToCart(
                $request->product_id,
                $request->quantity,
                $request->options ?? []
            );

            return $this->success([
                'item' => $item->load('product'),
                'cart_summary' => $this->cartService->getCartSummary()
            ], 'Product Added To Cart Successfully!');
        }
        catch(\Exception $e)
        {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function updateQuantity(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), ['quantity' => 'required|integer|min:1|max:99']);

        if ($validator->fails())
        {
            return $this->validationError($validator->errors());
        }

        try
        {
            $item = $this->cartService->updateQuantity($productId, $request->quantity);

            if (!$item)
            {
                return $this->notFound('Product Not Found On Cart!');
            }

            return $this->success([
                'item' => $item->load('product'),
                'cart_summary' => $this->cartService->getCartSummary(),
            ], 'Cart Updated Successfully!');
        }

        catch(\Exception $e) 
        {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function removeItem($productId)
    {
        try 
        {
            $removed = $this->cartService->removeFromCart($productId);

            if (!$removed)
            {
                return $this->notFound('Product Not Found On Cart!');
            }

            return $this->success([
                'cart_summary' => $this->cartService->getCartSummary(),
            ], 'Product Removed From Cart!');

        }
        catch (\Exception $e)
        {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function clear()
    {
        try 
        {
            $this->cartService->clearCart();

            return $this->success([
                'cart_summary' => $this->cartService->getCartSummary(),
            ], 'Cart Cleared Successfully!');
        }
        catch(\Exception $e)
        {
            return $this->$error($e->getMessage(), 500);
        }
    }

    public function getCount()
    {
        try 
        {
            $count = $this->cartService->getCartItemsCount();

            return $this->success([
                'count' => $count
            ]);
        }
        catch(\Exception $e)
        {
            return $this->error($e->getMessage(), 500);
        }

    }

    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50'
        ]);

        if ($validator->fails())
        {
            return $this->validationError($validator->errors());
        }

        try 
        {
            $result = $this->cartService->applyCoupon($request->code);

            return $this->success([
                'cart_summary' => $this->cartService->getCartSummary(),
                'coupon_result' => $result
            ], 'Coupon Applied Successfully!');

        }
        catch(\Exception $e)
        {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function removeCoupon()
    {
        try
        {
            $this->cartService->removeCoupon();

            return $this->success([
                'cart_summary' => $this->cartService->getCartSummary()
            ], 'Coupon Removed Successfully!');
        }
        catch(\Exception $e)
        {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function checkoutPreview()
    {
        try 
        {
            $preview = $this->cartService->convertToOrder();

            return $this->success([
                'checkout_preview' => $preview
            ], 'Checkout Preview Generated!');
        }
        catch(\Exception $e)
        {
            return $this->error($e->getMessage(), 400);
        }
    }

}
