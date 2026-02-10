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
        // return response()->json('test Connection');
      
        try{
          $credentials  = $request->only('email','password');
        }catch(InvalidCredentialsException $e){
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ],$e->getCode()
            );
           
        }catch(\Exception $e){
            return response->json([
                'success' => false,
                'message' => 'Internal server error'
            ],500);
        }

    }

    
}
