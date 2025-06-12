<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheKey = 'food_items_all';

        $fullKey = config('cache.prefix') . $cacheKey;
        \Log::info('🔍 Salvare cache sub cheia: ' . $fullKey);

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            return FoodItem::with('category:id,name')->get()->map(function($item) {
                $presignedUrl = Storage::disk('s3')->temporaryUrl(
                    $item->img_path,
                    now()->addMinutes(20)
                );

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'calories' => $item->calories,
                    'price' => $item->price,
                    'category_id' => $item->category_id,
                    'category_name' => optional($item->category)->name,
                    'image_url' => $presignedUrl,
                ];
            });
        });

        $value = \Cache::get($cacheKey);
        \Log::info('🧾 Cache brut: ' . json_encode($value));

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'calories' => 'required|integer',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);
        
            FoodItem::create($request->all());

            Cache::forget('food_items_all');

            return response()->json(['message' => 'Produs adaugat cu success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = FoodItem::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'calories' => 'required|integer',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        $item->update($request->all());

        Cache::forget('food_items_all');

        return response()->json(['message' => 'Produs actualizat cu success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = FoodItem::findOrFail($id);
        $item->delete();

        Cache::forget('food_items_all');

        return response()->json(['message' => 'Produs sters']);
    }
}
