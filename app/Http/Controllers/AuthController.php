<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        // Use session guard
        Auth::guard('web')->login($user);

        // Redirect based on role
        if ($user->role === 'tenant') {
            return redirect()->route('tenant.dashboard');
        }

        return redirect()->route('home'); // fallback
    }


   public function logout(Request $request)
{
    $accessToken = $request->bearerToken();

    if ($accessToken) {
        $request->user()->tokens()
            ->where('token', hash('sha256', $accessToken))
            ->delete();
    }

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
}

    public function tenantLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();

        return redirect()->intended('/tenant/dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials provided.',
    ]);
}


}
