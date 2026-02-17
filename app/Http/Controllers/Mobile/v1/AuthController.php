<?php
namespace App\Http\Controllers\Mobile\v1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\mobile\AuthService;
use App\Exceptions\Auth\InvalidCredentialsException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        
        try {
            $credentials = $request->only('email', 'password');
            $user = $this->authService->attemptLogin($credentials);
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('authToken')->plainTextToken,
                ]
            ]);
       }catch(InvalidCredentialsException $e){
              return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }catch(\Exception $e){
              return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    
    }
}
