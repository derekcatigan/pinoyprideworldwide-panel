<?php

namespace App\Http\Controllers\Shared;

use App\Enum\BoxType;
use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\BoxLocation;
use App\Models\BoxType as ModelsBoxType;
use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BoxController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $boxesQuery = Box::with(['boxType', 'branch', 'destination'])->latest();

        if ($user->role !== UserRole::Admin) {
            $boxesQuery->where('branch_id', $user->branch_id);
        }

        $boxes = $boxesQuery->paginate(10);

        return Inertia::render('views/shared/BoxList', [
            'boxes' => $boxes,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $branches = Branch::query()
            ->when($user->role !== UserRole::Admin, fn($q) => $q->whereKey($user->branch_id))
            ->get();

        $exclude = match ($user->role) {
            UserRole::Admin    => [BoxType::ODD],
            UserRole::BranchAdmin    => [BoxType::ODD],
            UserRole::Agent => [BoxType::ODD],
            default            => [BoxType::ODD],
        };

        return Inertia::render('views/shared/BoxCreate', [
            'branches'  => $branches,
            'locations' => BoxLocation::all(),
            'boxes' => BoxType::options($exclude),
            'userBranchId' => $user->branch_id,
            'existingBoxTypes' => ModelsBoxType::select('box_type', 'length', 'height', 'width', 'price')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'boxType' => [
                'required',
                Rule::in(
                    array_column(BoxType::options(
                        match ($user->role) {
                            UserRole::Admin    => [BoxType::ODD],
                            UserRole::BranchAdmin    => [BoxType::ODD],
                            UserRole::Agent => [BoxType::ODD],
                            default            => [BoxType::ODD],
                        }
                    ), 'value')
                ),
            ],
            'length' => 'required|numeric',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'price' => 'required|numeric',
            'location_id' => 'required|uuid',
            'branch_id' => 'nullable|uuid',
        ]);

        // Check if the same box type already exists in this location
        $existingBox = Box::where('location_id', $validated['location_id'])
            ->whereHas('boxType', function ($query) use ($validated) {
                $query->where('box_type', $validated['boxType'])
                    ->where('length', $validated['length'])
                    ->where('height', $validated['height'])
                    ->where('width',  $validated['width']);
            })
            ->where('branch_id', $validated['branch_id'])
            ->first();

        if ($existingBox) {
            return back()->withErrors([
                'error' => 'This box type with the same dimensions already exists in this location.'
            ]);
        }

        try {
            DB::beginTransaction();

            $boxtype = ModelsBoxType::create([
                'box_type' => $validated['boxType'],
                'length'   => $validated['length'],
                'height'   => $validated['height'],
                'width'    => $validated['width'],
                'price'    => $validated['price'],
            ]);

            Box::create([
                'location_id' => $validated['location_id'],
                'branch_id' => $validated['branch_id'],
                'box_type_id' => $boxtype->id,
            ]);

            DB::commit();
            return back()->with(['success' => 'Box added successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }

    public function destroy(Box $box)
    {
        try {
            $box->delete();

            return back()->with('success', 'Box removed successfully!');
        } catch (Exception $e) {
            Log::error('Delete error: ' . $e->getMessage());
            return back()->withErrors('Something went wrong while deleting the Box.');
        }
    }
}
