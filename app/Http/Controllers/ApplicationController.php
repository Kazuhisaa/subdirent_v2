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
}
