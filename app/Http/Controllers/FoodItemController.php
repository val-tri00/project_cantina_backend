<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FoodItem::with('category:id,name')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'calories' => $item->calories,
                'price' => $item->price,
                'category_id' => $item->category_id,
                'category_name' => optional($item->category)->name,
            ];
        });
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
        $item = FoodItem::findOrFaile($id);

        $request->validate([
            'name' => 'required',
            'calories' => 'required|integer',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        $item->update($request->all());

        return response()->json(['message' => 'Produs actualizat cu success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = FoodItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Produs sters']);
    }
}
