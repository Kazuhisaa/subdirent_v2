<?php

namespace App\Http\Controllers;
use App\Models\Tenant;
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
        'email'        => 'required|email|unique:applications,email',
        'contact_num'  => 'required|string|max:15',
        
        ]);

        $application = Application::create($credentials);
        return response()-> json([
            'message' => 'Application Successfuly Created',
            'data' => $application
        ],200);
    }

    public function update(Request $request, $id){

         $credentials =$request -> validate([
        'unit_id' => 'required|exists:units,id',
        'first_name'   => 'required|string|max:50',
        'middle_name'  => 'nullable|string|max:50',
        'last_name'    => 'required|string|max:50',
        'email'        => 'required|email|unique:applications,email,' . $id,
        'contact_num'  => 'required|string|max:15',
        ]);

            $application = Application::findOrFail($id);
            $application ->update($credentials);
            return response()->json([
                'message' => 'Application Updated Successfully',
                'application' => $application
            ],200);

    }

    public function archive($id){
            $application = Application::findOrFail($id);
            $application->delete();
            
            return response()-> json([
                'Message' => 'Application Archived Successfully',
                'data' => $application
            ]);
    }

    public function viewArchive(){

        $archived = Application::onlyTrashed()->get();
        return response()->json($archived);
    }
    

public function approve($id)
{
    $application = Application::findOrFail($id);
    $application->status = 'Approved';
    $application->save();

    // gawa ng bagong record sa db para sa tenant
    $tenant = new Tenant();
    $tenant->first_name  = $application->first_name;
    $tenant->middle_name = $application->middle_name;
    $tenant->last_name   = $application->last_name;
    $tenant->email       = $application->email;
    $tenant->contact_num = $application->contact_num;
    $tenant->unit_id     = $application->unit_id;
    $tenant->downpayment; 
    $tenant->monthly_payment; 
    $tenant->contract;
    $tenant->save();

    return response()->json([
        'message' => 'Application approved and tenant created!',
        'tenant'  => $tenant
    ]);
}


}
