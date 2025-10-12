<?php

namespace App\Http\Controllers\Shared;

use App\Enum\BoxType;
use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\BoxLocation;
use App\Models\BoxType as ModelsBoxType;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Order::with('orderBoxes', 'user', 'boxLocation')->latest();

        if ($user->role !== UserRole::Admin) {
            $query->where('branch_id', $user->branch_id);
        }

        $orders = $query->paginate(10);

        return Inertia::render('views/shared/OrderList', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $branches = Branch::query()
            ->when($user->role !== UserRole::Admin, fn($q) => $q->whereKey($user->branch_id))
            ->get();

        $agents = User::with('profile')
            ->where('role', 'agent')
            ->get();

        return Inertia::render('views/shared/OrderCreate', [
            'branches'  => $branches,
            'locations' => BoxLocation::all(),
            'boxTypes'     => BoxType::options(),
            'userBranchId' => $user->branch_id,
            'existingBoxes'  => Box::with('boxType')
                ->select('id', 'branch_id', 'location_id', 'box_type_id')
                ->with('boxType:id,box_type,length,height,width,price')
                ->get(),
            'agents'        => $agents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'senderName'       => 'required|string|max:255',
            'senderContact'    => ['required', 'string'],
            'senderEmail'      => 'nullable|email|max:255',
            'senderAddress'    => 'required|string|max:500',

            'receiverName'     => 'required|string|max:255',
            'receiverContact'  => ['required', 'string'],
            'receiverEmail'    => 'nullable|email|max:255',
            'receiverAddress'  => 'required|string|max:500',

            'invoiceNumber'    => 'required|string|max:100',
            'datePickUp'       => 'nullable|date',
            'boxes'            => 'required|array|min:1',
            'boxes.*.boxType'  => 'required|string|in:large,jumbo,odd',
            'boxes.*.length'   => 'required|numeric|min:1',
            'boxes.*.height'   => 'required|numeric|min:1',
            'boxes.*.width'    => 'required|numeric|min:1',
            'boxes.*.price'    => 'required|numeric|min:0',
            'boxes.*.quantity' => 'required|integer|min:1',
            'boxes.*.description' => 'nullable|string|max:2000',

            'location_id'      => 'required|uuid|exists:box_locations,id',
            'branch_id'        => 'required|uuid|exists:branches,id',
            'user_id'        => 'nullable|uuid|exists:users,id',
        ]);

        $user = Auth::user();

        $branch = Branch::findOrFail($validated['branch_id']);
        $fullInvoiceNumber = $branch->branch_code . '-' . $validated['invoiceNumber'];

        $exist = Order::where('invoice_number', $fullInvoiceNumber)->exists();

        if ($exist) {
            return back()->withErrors([
                'error' => "The invoice number already exists."
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $validated['user_id'] ?? $user->id,
                'branch_id' => $validated['branch_id'],
                'location_id' => $validated['location_id'],
                'invoice_number' => $fullInvoiceNumber,
                'sender_name' => Str::title($validated['senderName']),
                'sender_contact' => $validated['senderContact'],
                'sender_email' => $validated['senderEmail'],
                'sender_address' => Str::title($validated['senderAddress']),
                'receiver_name' => Str::title($validated['receiverName']),
                'receiver_contact' => $validated['receiverContact'],
                'receiver_email' => $validated['receiverEmail'],
                'receiver_address' => Str::title($validated['receiverAddress']),
                'status' => 'Picked-up'
            ]);

            foreach ($validated['boxes'] as $box) {
                $order->orderBoxes()->create([
                    'box_type' => $box['boxType'],
                    'quantity'    => $box['quantity'],
                    'length'      => $box['length'],
                    'height'      => $box['height'],
                    'width'       => $box['width'],
                    'fee'         => $box['price'],
                    'description' => $box['description'] ?? null,
                ]);
            }

            OrderTracking::create([
                'order_id'   => $order->id,
                'status'     => 'Picked-up',
                'remarks'    => 'Collected by authorized Agent',
                'created_by' =>  $validated['user_id'] ?? $user->id,
                'pick_up_date' => $validated['datePickUp'] ? Carbon::parse($validated['datePickUp']) : now(),
            ]);

            DB::commit();
            return back()->with(['success' => 'Order created successfully!']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }
}
