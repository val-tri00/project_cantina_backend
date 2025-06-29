<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request) {
        $query = Order::with(['user', 'items.food', 'paymentMethod', 'status']);

        // filtru dupa status
        if($request->has('status')) {
            $status = $request->status;
            $query->whereHas('status', function($q) use ($status) {
                if (is_numeric($status)) {
                    $q->where('id', $status);
                } else {
                    $q->where('status', $status); 
                }
            });
        }
        $orders = $query->orderByDesc('created_at')->take(20)->get();

        return response()->json($orders);
    }


    public function adminOrders() {
        $orders = Order::with(['user', 'items.food', 'paymentMethod', 'status'])
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
        return response()->json($orders);
    }


    public function updateStatus(Request $request, $id){
        
        \Log::info("Intrat in updateStatus pt comanda #$id", ['body' => $request->all()]);

        $request->validate([
            'status_id' => 'required|integer|exists:order_status,id',
        ]);

        try {
            $order = Order::findOrFail($id);
            $order->order_status_id = $request->status_id;
            $order->save();
        
            \Log::info("Status actualizat pt comanda #$id", ['nou_status' => $order->order_status_id]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'new_status' => $order->order_status_id
            ]);
        } catch (\Exception $e) {
            \Log::error("Eroare la update status pt comanda #$id", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Eroare la actualizarea statusului'
            ], 500);
        }
    }



    public function getAdminStats() {
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $revenue_today = Order::whereDate('created_at', today())->sum('total_price');

        $topFoods = OrderItem::select('food_id', DB::raw('SUM(quantity) as count'))
            ->groupBy('food_id')
            ->orderByDesc('count')
            ->take(3)
            ->with('food')
            ->get();

        $topItems = $topFoods->map(function ($item) {
            $imageUrl = null;

            if($item->food && $item->food->img_path) {
                $imageUrl = \Storage::disk('s3')->temporaryUrl(
                    $item->food->img_path,
                    now()->addMinutes(30)
                );
            }

            return [
                'name' => $item->food->name ?? 'N/A',
                'image_url' => $imageUrl ?? url('/images/default.png'),
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

}