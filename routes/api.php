<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DashboardController;


Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

});

Route::prefix('users')->group(function () {

    Route::get('/profile', [UserController::class, 'profile'])->middleware('auth:sanctum');
    Route::put('/profile', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
});

// Products Routes - Public 
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/category/{category}', [ProductController::class, 'byCategory']);
Route::get('/products/search/{keyword}', [ProductController::class, 'search']);

Route::middleware(['auth:sanctum','permission:manage-products'])->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->middleware('can:create,App\Models\Product');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('can:update,product');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('can:delete,product');
});


// Categories Routes 

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/categories/search/{keyword}', [CategoryController::class, 'search']);
Route::get('/categories/parent/{parent}', [CategoryController::class, 'byParent']);

Route::middleware(['auth:sanctum', 'permission:manage-categories'])->group(function () {
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('can:create,App\Models\Category');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('can:update,category');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('can:delete,category');
});

// Orders Routes 
Route::apiResource('orders', OrderController::class)->middleware('auth:sanctum');
Route::get('orders/user/{user}', [OrderController::class, 'byUser'])->middleware('auth:sanctum');

// Dashboard Routes 
Route::prefix('dashboard')->middleware(['auth:sanctum' , 'admin'])->group(function() {

    Route::get('/stats', [DashboardController::class, 'stats']);
    Route::get('/recent-orders', [DashboardController::class, 'recentOrders']);
    Route::get('/top-products', [DashboardController::class, 'topProducts']);

});

// Public Routes 

Route::get('/home', function () {

    return response()->json([

    'message' => 'welcome to Ecommerce API',
    'version' => '1',     
    'endpoints' => 
    [

        'auth' => '/api/auth',
        'products' => '/api/products',
        'categories' => '/api/categories',
        'orders' => '/api/orders',
        'dashboard' => '/api/dashboard'

    ]   
    ]);
});