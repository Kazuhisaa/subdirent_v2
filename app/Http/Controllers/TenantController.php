<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
  public function index(){
        $application = Tenant::with('unit')->get();
        return response()->json($application);
    }


    public function show($id){
      $application = Tenant::with('unit')->where('id',$id)->get();
          return response()->json($application);
    }

    
}
