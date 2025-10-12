<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\ContainerOrder;
use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TrackingStatusController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = ContainerOrder::with([
            'container.branch',
            'order.user',
            'order.boxLocation',
            'order.orderBoxes',
        ])->latest();

        if ($user->role !== UserRole::Admin) {
            $query->whereHas('container', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        $containerOrders = $query->paginate(12);

        return Inertia::render('views/admin/TrackingStatus', [
            'containerOrders' => $containerOrders,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'container' => 'required|string|exists:containers,container_number',
            'invoice' => 'nullable|string|exists:orders,invoice_number',
            'status' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $container = Container::where('container_number', $validated['container'])->firstOrFail();

        $ordersQuery = Order::query();

        if (!empty($validated['invoice'])) {
            // Update specific order
            $ordersQuery->where('invoice_number', $validated['invoice']);
        } else {
            // Update all orders linked to the container
            $ordersQuery->whereHas('containerOrders', function ($q) use ($container) {
                $q->where('container_id', $container->id);
            });
        }

        $orders = $ordersQuery->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'No matching orders found for this container.');
        }

        foreach ($orders as $order) {
            OrderTracking::create([
                'order_id' => $order->id,
                'container_id' => $container->id,
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => $user->id,
                'pick_up_date' => now(),
            ]);

            $order->update(['status' => $validated['status']]);
        }

        return back()->with('success', 'Tracking status updated successfully.');
    }
}
