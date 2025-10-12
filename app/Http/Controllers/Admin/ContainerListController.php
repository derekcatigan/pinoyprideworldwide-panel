<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use App\Exports\ContainerExport;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\ContainerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ContainerListController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Container::with([
            'branch',
            'containerOrders.order.orderBoxes'
        ])->latest();

        if ($user->role !== UserRole::Admin) {
            $query->where('branch_id', $user->branch_id);
        }

        $containers = $query->paginate(10);

        // Add computed total_boxes but keep full relationships
        $containers->getCollection()->transform(function ($container) {
            $totalBoxes = $container->containerOrders->sum(function ($co) {
                return $co->order?->orderBoxes->sum('quantity') ?? 0;
            });

            $container->total_boxes = $totalBoxes;
            return $container;
        });

        return Inertia::render('views/admin/ViewContainers', [
            'containerOrders' => $containers,
        ]);
    }

    public function export($id)
    {
        $container = Container::with([
            'branch',
            'containerOrders.order.orderBoxes'
        ])->findOrFail($id);

        $fileName = 'container_' . $container->container_number . '.csv';

        return Excel::download(new ContainerExport($container), $fileName);
    }
}
