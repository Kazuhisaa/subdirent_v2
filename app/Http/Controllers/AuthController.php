<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('admin.login'); // your shared login form
    }

    /**
     * Handle login for both Admin and Tenant
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([ 
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ Admin Login: Create Sanctum token for API
            if ($user->role === 'admin') {
                // Create token with 'admin' ability
                $token = $user->createToken('admin-token', ['admin'])->plainTextToken;

                // Store in session for JS access
                session(['admin_api_token' => $token]);

                return redirect()->route('admin.home')->with('status', 'Welcome, Admin!');
                return redirect()->route('admin.home')->with('admin_api_token', $token);

            }

            // ✅ Tenant Login: Redirect to tenant dashboard
            if ($user->role === 'tenant') {
                return redirect()->route('tenant.home')->with('status', 'Welcome, Tenant!');
            }

            // 🚫 If neither role
            Auth::logout();
            return back()->withErrors(['email' => 'Unauthorized role.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Logout for both web + API users
     */
    public function logout(Request $request)
    {
    
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Logged out successfully.');
    }


public function apiLogin(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = Auth::user();

    // Create API token using Laravel Sanctum
    $token = $user->createToken('API Token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token
    ]);
}



}
