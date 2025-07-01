<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\MeniuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\Admin\FoodItemController as AdminFoodItemController;


Route::get('/test', function () {
    return response()->json(['message' => 'API routes are loaded!']);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'userProfile']);
Route::get('/meniu', [MeniuController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/food-items', [FoodItemController::class, 'index']);
    Route::post('/food-items', [FoodItemController::class, 'store']);
    Route::put('/food-items/{id}', [FoodItemController::class, 'update']);
    Route::delete('/food-items/{id}', [FoodItemController::class, 'destroy']);

    Route::get('/categories', [FoodItemController::class, 'index']);

});


Route::middleware('auth:sanctum')->group(function() {
    Route::post('/orders', [OrderController::class, 'store']); 
    Route::get('/orders', [OrderController::class, 'index']); 
    Route::get('/orders/{id}', [OrderController::class, 'show']); 
    Route::get('/user/orders', [OrderController::class, 'index']);
});



Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/orders', [OrderController::class, 'adminOrders']);
    Route::get('/stats', [OrderController::class, 'getAdminStats']);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('/orders/pending', [OrderController::class, 'pendingOrders']);
    
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/produse', [AdminFoodItemController::class, 'index']);
    Route::post('/produse', [AdminFoodItemController::class, 'store']);
    Route::put('/produse/{id}', [AdminFoodItemController::class, 'update']);
    Route::delete('/produse/{id}', [AdminFoodItemController::class, 'destroy']);
});