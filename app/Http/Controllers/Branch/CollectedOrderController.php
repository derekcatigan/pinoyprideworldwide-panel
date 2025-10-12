<?php

namespace App\Http\Controllers\Branch;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderBox;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CollectedOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = OrderBox::with('order')
            ->whereDoesntHave('order.trackings', function ($q) {
                $q->where('status', 'Received at Warehouse');
            })
            ->latest();

        if ($user->role !== UserRole::Admin) {
            $query->whereHas('order', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        $orders = $query->paginate(10);
        return Inertia::render('views/branch/OrderCollected', [
            'orders' => $orders,
        ]);
    }

    public function collect(Request $request)
    {
        $validated = $request->validate([
            'orderBoxes' => 'required|array|min:1',
            'orderBoxes.*' => 'uuid|exists:order_boxes,id',
        ]);

        $userId = Auth::id();

        $orderBoxes = OrderBox::query()
            ->whereIn('id', $validated['orderBoxes'])
            ->get();

        // Group by order_id (each order may have multiple boxes)
        $groupedByOrder = $orderBoxes->groupBy('order_id');

        foreach ($groupedByOrder as $orderId => $boxes) {
            // Prevent duplicate tracking for same order/status
            $exists = OrderTracking::where('order_id', $orderId)
                ->where('status', 'Received at Warehouse')
                ->exists();

            if (! $exists) {
                OrderTracking::create([
                    'order_id' => $orderId,
                    'status' => 'Received at Warehouse',
                    'remarks' => 'Boxes collected at Warehouse',
                    'created_by' => $userId,
                    'pick_up_date' => now(),
                ]);

                Order::where('id', $orderId)->update([
                    'status' => 'Received at Warehouse',
                ]);
            }
        }

        return back()->with('success', 'Selected boxes have been marked as collected.');
    }
}
