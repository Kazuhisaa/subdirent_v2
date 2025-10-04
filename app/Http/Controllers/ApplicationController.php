<?php

namespace App\Http\Controllers;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    //

    public function index(){
        $application = Application::with('unit')->get();
        return response()->json($application);
    }


    public function show($id){
      $application = Application::with('unit')->where('id',$id)->get();
          return response()->json($application);
    }

    public function store(Request $request){
        $credentials =$request -> validate([
        'unit_id' => 'required|exists:units,id',
        'first_name'   => 'required|string|max:50',
        'middle_name'  => 'nullable|string|max:50',
        'last_name'    => 'required|string|max:50',
        'email'        => 'required|email|unique:bookings,email',
        'contact_num'  => 'required|string|max:15',
        
        ]);

        $credentials = Application::create($credentials);
        return response()-> json([
            'message' => 'Application Successfuly Created',
            'data' => $credentials,
        ],200);
    }

    public function update(Request $request, Application $id){

         $credentials =$request -> validate([
        'unit_id' => 'required|exists:units,id',
        'first_name'   => 'required|string|max:50',
        'middle_name'  => 'nullable|string|max:50',
        'last_name'    => 'required|string|max:50',
        'email'        => 'required|email|unique:bookings,email',
        'contact_num'  => 'required|string|max:15',
        ]);

    
    
    }



}
