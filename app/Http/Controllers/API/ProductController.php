<?php

namespace App\Http\Controllers\API;

use Str;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->where('status', true)->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {


        if (!$request->user()->isAdmin())
        {
            return response() ->json([
                'message' => 'UnAuthorized - Admin Only'
            ], 403);
        }

        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image'))
        {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        if ($request->hasFile('images'))
        {
            $imagesPaths = [];

            foreach($request->file('images') as $image)
            {
                $imagesPaths[] = $image->store('products/gallery', 'public');
            }

            $validated['images'] = json_encode($imagesPaths);
        }

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product Created Successfully!',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('category')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        if (!$request->user()->isAdmin())
        {
            return response()->json([
                'message' => 'UnAuthorized  - Admin Only'
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'product deleted successfully'
        ]);
    }

    public function byCategory(Category $category) 
    {
        $products = Product::where('category_id', $category->id)->where('status', true)->paginate(12);

        return response()->json([
            'success' => true,
            'category' => $category,
            'data' => $products
        ]);
    }

    public function search($keyword)
    {
        $products = Product::where('status', true)->where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%')
                  ->orWhere('sku', 'like', '%' . $keyword . '%');
        })->paginate(12);

        return response()->json([
            'success' => 'true',
            'keyword' => $keyword,
            'data' => $products
        ]);
    }

    public function featured()
    {
        $products = Product::with('category')->where('featured', true)->where('status', true)->limit(8)->get();

        return respone()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
