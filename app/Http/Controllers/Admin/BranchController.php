<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(10);
        return Inertia::render('views/admin/BranchList', [
            'branches' => $branches
        ]);
    }

    public function create()
    {
        return Inertia::render('views/admin/BranchCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch'   => 'required|string',
            'owner'  => 'required|string',
        ]);

        $branchName = Str::title($validated['branch']);
        $branchOwner = Str::title($validated['owner']);

        $exists = Branch::where('branch_name', $branchName)
            ->where('branch_owner', $branchOwner)
            ->first();

        if ($exists) {
            return back()->withErrors('This branch already exists for the specified owner. Please choose a different branch name or owner.');
        }

        /*
             * Handles branch code which get the first and last letter of branch name 
             */
        $branchName = $validated['branch'];
        $firstLetter = strtoupper($branchName[0]);
        $lastLetter = strtoupper($branchName[strlen($branchName) - 1]);
        $code = "PPW-{$firstLetter}{$lastLetter}";

        try {
            DB::beginTransaction();

            Branch::create([
                'branch_code' => $code,
                'branch_name' => Str::title($branchName),
                'branch_owner'  => Str::title($branchOwner),
            ]);

            DB::commit();

            return back()->with('success', 'Branch created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors('Something went wrong. Please try again later');
        }
    }

    public function destroy(Branch $branch)
    {
        try {
            $branch->delete();

            return back()->with(['success' => 'Branch deleted successfully!']);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }
}
