<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\mobile\AuthService;
use App\Exceptions\Auth\InvalidCredentialsException;
class AuthController extends Controller
{
    //
   
    protected $authService;
    public function __construct(AuthService $authService){
           $this->authService = $authService;
    }


    public function login(Request $request){
        $credentials  = $request->only('email','password');

        // Attempt to log in using the AuthService.
        // The AuthService is expected to throw InvalidCredentialsException on failure.
        $user = $this->authService->attemptLogin($credentials);

        // If login is successful, return a success response with the user's token or data
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user, // Or just relevant user data
                'token' => $user->createToken('authToken')->plainTextToken, // Example token generation
            ]
        ]);
    }

    
}
