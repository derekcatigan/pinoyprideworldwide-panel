<?php

namespace App\Http\Controllers\Shared;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Container;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ContainerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $branches = Branch::query()
            ->when($user->role !== UserRole::Admin, fn($q) => $q->whereKey($user->branch_id))
            ->get();

        $containers = Container::with('branch')->latest()->paginate(10);
        return Inertia::render('views/admin/ContainerList', [
            'branches' => $branches,
            'containers' => $containers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'containerNumber' => 'required|string',
            'containerBranch' => 'nullable|uuid'
        ]);

        try {
            DB::beginTransaction();

            Container::create([
                'container_number' => Str::upper($validated['containerNumber']),
                'branch_id' => $validated['containerBranch'],
                'status' => $validated['containerBranch'] ? 'assigned' : 'available'
            ]);

            DB::commit();
            return back()->with(['success' => 'Container added successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }

    public function update(Request $request, Container $containerID)
    {
        $validated = $request->validate([
            'containerBranchUpdate' => 'required|uuid'
        ]);

        try {
            DB::beginTransaction();

            $containerID->update([
                'branch_id' => $validated['containerBranchUpdate'],
                'status' => "assigned"
            ]);

            DB::commit();
            return back()->with(['success' => 'Assigned container successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }

    public function destroy(Container $container)
    {
        try {
            $container->delete();

            return back()->with('success', 'Container removed successfully!');
        } catch (Exception $e) {
            Log::error('Delete error: ' . $e->getMessage());
            return back()->withErrors('Something went wrong while deleting the Container.');
        }
    }
}
