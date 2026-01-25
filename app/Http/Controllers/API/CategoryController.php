<?php

namespace App\Http\Controllers\API;

use Str;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with(['parent', 'children'])->where('status', true)->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin())
        {
            return response()->json([
                'message' => 'unAuthorized, Admin Access Required!'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'boolean'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'status' => $request->status ?? true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category->load('parent', 'children', 'products')
        ]);
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
            return response()->json([
                'message' => 'Un Authorized, Admin Access Required!'
            ], 403);
        }

        if ($category->products()->count() > 0)
        {
            return response()->json([
                'message' => 'Can not Delete Category, Please Delete Products First'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category Deleted Successfully!'
        ]);
    }

    /**
     * Get Categories By Parent.
     */
    public function byParent($parentId)
    {
        $categories = Category::where('parent_id', $parentId)->where('status', true)->with('children')->get();

        return response()->json([
            'success' => true,
            'parent_id' => $parentId,
            'data' => $categories
        ]);
    }


}
