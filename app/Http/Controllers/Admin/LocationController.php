<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoxLocation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index()
    {
        $locations = BoxLocation::latest()->paginate(10);
        return Inertia::render('views/admin/Destination', [
            'locations' => $locations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'location' => 'required|string|max:255|unique:box_locations,location',
            ],
            [
                'location.unique' => 'This destination already exists. Please enter a different one.'
            ]
        );

        try {
            DB::beginTransaction();

            BoxLocation::create([
                'location' => Str::title($validated['location']),
            ]);

            DB::commit();
            return back()->with(['success' => 'Location created successfully!']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        };
    }

    public function destroy(BoxLocation $location)
    {
        try {
            $location->delete();
            return back()->with(['success' => 'Location deleted successfully!']);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }
}
