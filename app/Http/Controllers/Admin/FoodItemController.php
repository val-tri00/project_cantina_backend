<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodItemController extends Controller
{
    public function index()
    {
        $produse = FoodItem::with('category:id,name')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'calories' => $item->calories,
                'price' => $item->price,
                'category_id' => $item->category_id,
                'category_name' => optional($item->category)->name,
                'image_url' => Storage::disk('s3')->temporaryUrl(
                    $item->img_path,
                    now()->addMinutes(20)
                )
            ];
        });
    
        return response()->json($produse);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'calories' => 'nullable|integer',
            'price' => 'required|numeric',
            'img_path' => 'required|string'
        ]);

        return FoodItem::create($validated);
    }

    public function update(Request $request, $id)
    {
        $food = FoodItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'calories' => 'nullable|integer',
            'price' => 'required|numeric',
            'img_path' => 'required|string'
        ]);

        $food->update($validated);

        return $food;
    }

    public function destroy($id)
    {
        $food = FoodItem::findOrFail($id);
        $food->delete();

        return response()->json(['success' => true]);
    }
}
