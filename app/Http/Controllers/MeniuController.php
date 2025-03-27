<?php

namespace App\http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class MeniuController extends Controller{
    public function index(): JsonResponse{
        $categorii = Category::with(['foodItems']) -> get();

        return response()->json($categorii);
    }
}