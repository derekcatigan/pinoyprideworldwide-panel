<?php

namespace App\Http\Controllers\Branch;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Branch;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BranchAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $users = User::with(['profile', 'branch'])
            ->when($user->role !== UserRole::Admin->value, function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('views/branch/BranchAccountPage', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $branches = Branch::query()
            ->when($user->role !== UserRole::Admin->value, function ($query) use ($user) {
                $query->where('id', $user->branch_id);
            })
            ->select('id', 'branch_name')
            ->get();

        $roles = [
            [
                'name' => UserRole::Agent->displayName(),
                'value' => UserRole::Agent->value,
            ]
        ];

        return Inertia::render('views/branch/BranchAccountCreate', [
            'branches' => $branches,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|unique:users,contact',
            'role' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'gender'         => 'required|string',
            'birthdate'      => 'required|date|before:today',
            'street'         => 'nullable|string|max:255',
            'barangay'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'country'        => 'required|string|max:255',
        ]);

        if ($validated['role'] === 'admin' && !empty($validated['branch_id'])) {
            return back()->withErrors([
                'error' => 'Admin cannot be assigned to a branch.'
            ]);
        }

        try {
            DB::beginTransaction();

            $userCode = User::generateUserCode($validated['role']);

            $user = User::create([
                'user_code' => $userCode,
                'email'     => $validated['email'],
                'contact'   => $validated['contact'],
                'role'      => $validated['role'],
                'branch_id' => $validated['branch_id'] ?? null,
                'password'  => Hash::make($validated['password'] ?? 'password'),
            ]);

            $profile = Profile::create([
                'user_id'    => $user->id,
                'first_name' => Str::title($validated['first_name']),
                'last_name'  => Str::title($validated['last_name']),
                'gender'     => $validated['gender'],
                'birthdate'  => $validated['birthdate'],
            ]);

            Address::create([
                'profile_id' => $profile->id,
                'street' => Str::title($validated['street']) ?? null,
                'barangay' => Str::title($validated['barangay']) ?? null,
                'city' => Str::title($validated['city']),
                'province' => Str::title($validated['province']),
                'country' => Str::title($validated['country']),
            ]);

            DB::commit();
            return back()->with(['success' => 'Account created successfully!']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong. Please try again later.']);
        }
    }
    public function edit(User $user)
    {
        $authUser = Auth::user();

        $branches = Branch::query()
            ->when($authUser->role !== UserRole::Admin->value, function ($query) use ($authUser) {
                $query->where('id', $authUser->branch_id);
            })
            ->select('id', 'branch_name')
            ->get();

        $roles = [
            [
                'name' => UserRole::BranchAdmin->displayName(),
                'value' => UserRole::BranchAdmin->value,
            ],
            [
                'name' => UserRole::Agent->displayName(),
                'value' => UserRole::Agent->value,
            ]
        ];

        return Inertia::render('views/branch/BranchAccountEdit', [
            'user' => $user->load(['profile.address']),
            'branches' => $branches,
            'roles'    => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email'   => "required|email|max:255|unique:users,email,{$user->id}",
            'contact' => "required|string|min:11|max:20|unique:users,contact,{$user->id}",
            'role'           => 'required|string',
            'branch_id'      => 'nullable|exists:branches,id',
            'password'       => 'nullable|string|min:8|confirmed',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'gender'         => 'required|string',
            'birthdate'      => 'required|date|before:today',
            'street'         => 'nullable|string|max:255',
            'barangay'       => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'country'        => 'required|string|max:255',
        ]);

        if ($validated['role'] === 'admin' && !empty($validated['branch_id'])) {
            return back()->withErrors([
                'error' => 'Admin cannot be assigned to a branch.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update User
            $user->update([
                'email'     => $validated['email'],
                'contact'   => $validated['contact'],
                'role'      => $validated['role'],
                'branch_id' => $validated['branch_id'] ?? null,
                'password'  => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            ]);

            // Update Profile
            $user->profile()->update([
                'first_name' => Str::title($validated['first_name']),
                'last_name'  => Str::title($validated['last_name']),
                'gender'     => $validated['gender'],
                'birthdate'  => $validated['birthdate'],
            ]);

            // Update Address
            $user->profile->address()->update([
                'street' => $validated['street'] ? Str::title($validated['street']) : null,
                'barangay'       => $validated['barangay'] ? Str::title($validated['barangay']) : null,
                'city'           => Str::title($validated['city']),
                'province' => Str::title($validated['province']),
                'country'        => Str::title($validated['country']),
            ]);

            DB::commit();

            return back()->with('success', 'Account updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user account: ' . $e->getMessage());
            return back()->withErrors('Something went wrong. Please try again later.');
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::transaction(function () use ($user) {
                $role = $user->role instanceof UserRole
                    ? $user->role->value
                    : $user->role;

                // Prevent deleting last admin
                if ($role === UserRole::Admin->value) {
                    $isLastAdmin = User::where('role', UserRole::Admin->value)->count() <= 1;
                    if ($isLastAdmin) {
                        throw new \RuntimeException('Cannot delete the last remaining admin.');
                    }
                }

                // Delete related data safely
                $user->profile?->address()?->delete();
                $user->profile()?->delete();

                // Delete user
                $user->delete();
            });

            return back()->with('success', 'User deleted successfully!');
        } catch (Exception $e) {
            Log::error('Failed to delete user', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => $e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'Something went wrong. Please try again.',
            ]);
        }
    }
}
