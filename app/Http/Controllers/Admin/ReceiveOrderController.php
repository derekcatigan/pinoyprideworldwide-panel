<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReceiveOrderController extends Controller
{
    public function index()
    {
        // Fetch branches that have at least one order
        $branches = Branch::query()
            ->whereHas('orders')
            ->latest()
            ->paginate(10);

        // Preload all box totals, grouped by branch, location, and box type
        $boxTotals = DB::table('orders')
            ->join('order_boxes', 'orders.id', '=', 'order_boxes.order_id')
            ->join('box_locations', 'orders.location_id', '=', 'box_locations.id')
            ->select(
                'orders.branch_id',
                'box_locations.location',
                'order_boxes.box_type',
                DB::raw('SUM(order_boxes.quantity) as total_boxes')
            )
            ->groupBy('orders.branch_id', 'box_locations.location', 'order_boxes.box_type')
            ->get();

        return Inertia::render('views/admin/ReveiveOrder', [
            'branches' => $branches,
            'boxTotals' => $boxTotals,
        ]);
    }
}
