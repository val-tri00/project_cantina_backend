<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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


    public function updateStatus(Request $request, $id) {
        $validated = $request->validate([
            'status_id' => 'required|integer|exists:order_status,id'
        ]);
    
        $order = Order::findOrFail($id);
        $order->order_status_id = $validated['status_id'];
        $order->save();
    
        return response()->json(['success' => true]);
    }    


    public function getAdminStats() {
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $revenue_today = Order::whereDate('created_at', today())->sum('total_price');
    
        $topItems = OrderItem::select('food_id', DB::raw('SUM(quantity) as count'))
            ->groupBy('food_id')
            ->orderByDesc('count')
            ->with('food')
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->food->name,
                    'image_url' => $item->food && $item->food->img_path
                        ? Storage::disk('s3')->temporaryUrl($item->food->img_path, now()->addMinutes(20))
                        : url('/images/default.png'),
                    'count' => $item->count,
                ];
            });
    
        $totalRecent = Order::where('created_at', '>=', now()->subDays(30))->count();
        $orderSummary = Order::select('order_status_id', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('order_status_id')
            ->get()
            ->mapWithKeys(function ($row) use ($totalRecent) {
                $label = match ($row->order_status_id) {
                    1 => 'confirmed',
                    2 => 'in_progress',
                    3 => 'canceled',
                    4 => 'finished',
                    default => 'other',
                };
                return [$label => round(($row->total / max($totalRecent, 1)) * 100)];
            });
    
        return response()->json([
            'total_orders' => $totalOrders,
            'total_users' => $totalUsers,
            'revenue_today' => $revenue_today,
            'top_items' => $topItems,
            'order_summary' => $orderSummary,
        ]);
    }

    public function adminOrders() {
        $orders = Order::with(['user', 'items.food', 'paymentMethod', 'status'])
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
    
        return response()->json($orders);
    }    
    
}


