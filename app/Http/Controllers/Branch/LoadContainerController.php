<?php

namespace App\Http\Controllers\Branch;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\ContainerOrder;
use App\Models\Order;
use App\Models\OrderBox;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoadContainerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = OrderBox::with('order')
            ->whereDoesntHave('order.trackings', function ($q) {
                $q->where('status', 'Loaded to Container');
            })
            ->latest();

        if ($user->role !== UserRole::Admin) {
            $query->whereHas('order', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        $orders = $query->paginate(10);


        $containers = Container::with('branch')
            ->where('branch_id', $user->branch_id)
            ->whereDoesntHave('containerOrders.order.trackings', function ($query) {
                $query->whereIn('status', ['In Transit', 'Departed Vessel']);
            })
            ->latest()
            ->get();

        return Inertia::render('views/branch/LoadContainer', [
            'orders' => $orders,
            'containers' => $containers
        ]);
    }

    public function load(Request $request)
    {
        $validated = $request->validate([
            'container' => 'required|uuid|exists:containers,id',
            'orderBoxes' => 'required|array|min:1',
            'orderBoxes.*' => 'uuid|exists:order_boxes,id',
        ]);

        $userId = Auth::id();
        $containerId = $validated['container'];

        $orderBoxes = OrderBox::query()
            ->whereIn('id', $validated['orderBoxes'])
            ->get();

        // Group by order_id (each order may have multiple boxes)
        $groupedByOrder = $orderBoxes->groupBy('order_id');

        foreach ($groupedByOrder as $orderId => $boxes) {
            // Prevent duplicate tracking for same order/status
            $exists = OrderTracking::query()
                ->where('order_id', $orderId)
                ->where('status', 'Loaded to Container')
                ->exists();

            ContainerOrder::create([
                'container_id' => $containerId,
                'order_id' => $orderId,
            ]);

            if (! $exists) {
                OrderTracking::create([
                    'container_id' => $containerId,
                    'order_id' => $orderId,
                    'status' => 'Loaded to Container',
                    'remarks' => 'Boxes loaded into container.',
                    'created_by' => $userId,
                    'pick_up_date' => now(),
                ]);

                Order::where('id', $orderId)->update([
                    'status' => 'Loaded to Container',
                ]);
            }
        }

        return back()->with('success', 'Selected boxes have been marked as loaded into container.');
    }
}
