<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //


    public function login(){
        return response()->json('test Connection');
    }

    
}
