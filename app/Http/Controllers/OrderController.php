<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request) {
        logger('User:', [$request->user()]);

        if (!$request->user()) {
            \Log::error('User este NULL în store order!');
            return response()->json(['error' => 'Unauthorized in controller'], 401);
        }    

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|integer|exists:food_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method_id' => 'required|exists:payment_method,id',
            'pickup_time' => 'nullable|date|after:now',
        ]);

        return DB::transaction(function() use ($validated, $request) {
            $total = 0;
            foreach($validated['items'] as $item) {
                $price = \DB::table('food_items')->where('id', $item['food_id'])->value('price');
                $total += $price * $item['qty'];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_status_id' => 1, // daca pending
                'payment_method_id' => $validated['payment_method_id'],
                'total_price' => $total,
                'pickup_time' => $validated['pickup_time'] ?? null,
            ]);

            foreach($validated['items'] as $item) {
                $price = \DB::table('food_items')->where(('id'), $item['food_id'])->value('price');
                OrderItem::create([
                    'order_id' => $order->id,
                    'food_id' => $item['food_id'],
                    'quantity' => $item['qty'],
                    'price' => $price,
                ]);
            }
            return response()->json(['success' => true, 'order_id' => $order->id], 201);
        });
    }


    public function index(Request $request) {
        $orders = Order::with('items.food')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json($orders);
    }

    public function show($id, Request $request) {
        $order = Order::with('items.food')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        return response()->json($order);
    }
}
