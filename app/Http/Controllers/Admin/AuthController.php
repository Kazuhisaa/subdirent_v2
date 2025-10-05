<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found']);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password']);
        }

        // Login the user
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.home'); // Admin dashboard
            case 'tenant':
                return redirect()->route('tenant.dashboard'); // Tenant dashboard
            default:
                Auth::logout();
                return back()->withErrors(['email' => 'Unauthorized access']);
        }
    }

        public function logout(Request $request)
        {
            Auth::logout(); // remove the user from the session

            // Invalidate and regenerate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to homepage (not JSON)
            return redirect()->route('home')->with('status', 'Logged out successfully');

        }
    
}
