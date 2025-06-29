<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function stats(Request $request) {
        \Log::info('INTRAT IN DASHBOARD CONTROLLER!');
        $today = now()->startOfDay();

        $ordersToday = Order::where('created_at', '>=', $today)->count();
        \Log::info('ordersToday', ['val' => $ordersToday]);
        $usersTotal = User::count();
        \Log::info('usersTotal', ['val' => $usersTotal]);
        $revenueToday = Order::where('created_at', '>=', $today)->sum('total_price');
        \Log::info('revenueToday', ['val' => $revenueToday]);

        return response()->json([
            'orders' => $ordersToday, 
            'users' => $usersTotal, 
            'revenue' => $revenueToday, 
        ]);
    }

    public function orderSummary() {
        $totalOrders = Order::count();
        if($totalOrders === 0) {
            return response()->json([
                'confirmed' => 0, 
                'preparing' => 0, 
                'completed' => 0 
            ]);
        }

        $confirmed = Order::whereHas('status', fn($q) => $q->where('status', 'Confirmata'))->count();
        $preparing = Order::whereHas('status', fn($q) => $q->where('status', 'In pregatire'))->count();
        $completed = Order::whereHas('status', fn($q) => $q->where('status', 'Finalizata'))->count();

        return response()->json([
            'confirmed' => round(($confirmed / $totalOrders) * 100),
            'preparing' => round(($preparing / $totalOrders) * 100),
            'completed' => round(($completed / $totalOrders) * 100)
        ]);
    }

    public function topSales() {
        $topFoods = OrderItem::selectRaw('food_id, COUNT(*) as count')
            ->groupBy('food_id')
            ->orderByDesc('count')
            ->take(3)
            ->with('food')
            ->get();

        $result = $topFoods->map(function ($item) {
            $imageUrl = null;
            if($item->food && $item->food->img_path) {
                $imageUrl = \Storage::disk('s3')->temporaryUrl(
                    $item->food->img_path,
                    now()->addMinutes(20)
                );
            }
            
            return [
                'name' => $item->food->name ?? 'N/A',
                'count' => $item->count,
                'image' => $imageUrl,
            ];
        });

        return response()->json($result);
    }
}
