<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\MeniuController;
use Illuminate\Support\Facades\Route;


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
