<?php

namespace App\Http\Controllers\Auth;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function index()
    {
        return Inertia::render('auth/Login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                return back()->withErrors(['error' => 'Email not found.']);
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return back()->withErrors(['error' => 'Incorrect password.']);
            }

            Auth::login($user);
            $request->session()->regenerate();

            $route = match ($user->role) {
                UserRole::Admin => 'admin.dashboard',
                UserRole::BranchAdmin => 'branch.dashboard',
                UserRole::Agent => 'agent.dashboard'
            };

            return redirect()->intended(route($route));
        } catch (Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::flush();

        return redirect('/');
    }
}
