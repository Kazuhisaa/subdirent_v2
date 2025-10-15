<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function home(){
        return view('tenant.home');
    }
        

    public function index(){
        $application = Tenant::with('unit')->get();
        return response()->json($application);
    }


    public function show($id){
      $application = Tenant::with('unit')->where('id',$id)->get();
          return response()->json($application);
    }


    public function update(Request $request, $id){

        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name'=> 'nullable|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:tenants,email,' . $tenant->id,
            'contact_num'=> 'required|string|max:50',
            'unit_id'    => 'required|exists:units,id',
            'downpayment'=> 'required|numeric',
            'monthly_payment' => 'required|numeric',
            'contract' => 'required|numeric'
        ]);

        $tenant->update($validated);

        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => $tenant
        ]);
    }

    public function archive($id){
            $application = Tenant::findOrFail($id);
            $application->delete();
            
            return response()-> json([
                'Message' => 'Tenant Archived Successfully',
                'data' => $application
            ]);
    }

    public function viewArchive(){

        $archived = Tenant::onlyTrashed()->get();
        return response()->json($archived);
    }
    
    


    // public function index()
    // {
    //     return view('tenant.home');
    // }
}


