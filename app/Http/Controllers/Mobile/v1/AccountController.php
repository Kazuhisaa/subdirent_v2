<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    
  

public function show(){
 
      try {
           
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
