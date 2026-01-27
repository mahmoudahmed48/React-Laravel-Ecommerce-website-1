<?php

namespace App\Http\Controllers\API;

use Str;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with(['parent', 'children'])->where('status', true)->get();

        return $this->success($categories, $message = 'Category Fetched Correctly!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        if (!$request->user()->isAdmin())
        {
            return response()->json([
                'message' => 'unAuthorized, Admin Access Required!'
            ], 403);
        }

        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image'))
        {
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        $category = Category::create($validated);

        return $this->created($category, 'Category Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->success($category->load('parent', 'children', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        if (!$request->user()->isAdmin())
        {
            return $this->forbidden();
        }

        if ($category->products()->count() > 0)
        {
            return $this->error('Can not Delete Category With Products', 400);
        }

        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return $this->deleted('Category Deleted Successfully!');
    }

    /**
     * Get Categories By Parent.
     */
    public function byParent($parentId)
    {
        $categories = Category::where('parent_id', $parentId)->where('status', true)->with('children')->get();

        return $this->success([
            'parent_id' => $parentId,
            'categories' => $categories
        ]);
    }


}
