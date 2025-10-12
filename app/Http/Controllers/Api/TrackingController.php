<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TrackingController extends Controller
{
    public function show($invoice)
    {
        $order = Order::with([
            'trackings' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'boxLocation',
            'user'
        ])->where('invoice_number', $invoice)->first();

        if (! $order) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }

        return response()->json([
            'invoice_number' => $order->invoice_number,
            'status' => $order->status,
            'box_location' => $order->boxLocation?->location,
            'history' => $order->trackings->map(fn($t) => [
                'status' => $t->status,
                'message' => $t->remarks,
                'location' => $order->boxLocation?->location,
                'time' => $t->pick_up_date
                    ? Carbon::parse($t->pick_up_date)->format('M d Y')
                    : null,
            ]),
        ]);
    }
}
