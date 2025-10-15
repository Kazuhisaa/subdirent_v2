<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use App\Models\User;
use App\Models\Tenant;
use App\Mail\TenantMail;
use App\Models\Contract;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

  public function update(Request $request, $id)
{
    $credentials = $request->validate([
        'unit_id' => 'required|exists:units,id',
        'first_name' => 'required|string|max:50',
        'middle_name' => 'nullable|string|max:50',
        'last_name' => 'required|string|max:50',
        'email' => 'required|email|unique:applications,email,' . $id,
        'contact_num' => 'required|string|max:15',
        'monthly_rent' => 'nullable|numeric',
        'unit_price' => 'nullable|numeric',
        'downpayment' => 'nullable|numeric',
        'payment_due_date' => 'nullable|integer|min:1|max:31',
        'contract_duration' => 'nullable|string|max:20',
        'remarks' => 'nullable|string',
    ]);

    $application = Application::findOrFail($id);
    $unit = Unit::find($request->unit_id);
if ($unit) {
    // Make sure no commas
    $application->unit_price = (float) str_replace(',', '', $unit->unit_price);
}

    $application->update($credentials);

    return response()->json([
        'message' => 'Application Updated Successfully',
        'application' => $application
    ], 200);
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

    // validate kung may necessary fields
    if (!$application->monthly_rent || !$application->downpayment) {
        return response()->json([
            'error' => 'Please complete rent and downpayment details before confirming.'
        ], 422);
    }

    // mark as approved
    $application->status = 'Approved';
    $application->save();

    // Create tenant
    $tenant = Tenant::create([
        'first_name' => $application->first_name,
        'middle_name' => $application->middle_name,
        'last_name' => $application->last_name,
        'email' => $application->email,
        'contact_num' => $application->contact_num,
        'unit_id' => $application->unit_id,
    ]);

    // Create contract (auto 10-year)
    $startDate = Carbon::now();
    $endDate = $startDate->copy()->addYears(5);

    $contract = Contract::create([
        'tenant_id' => $tenant->id,
        'unit_id' => $application->unit_id,
        'contract_start' => $startDate,
        'contract_end' => $endDate,
        'contract_duration' => '5 years',
        'downpayment' => $application->downpayment,
        'monthly_payment' => $application->monthly_rent,
        'payment_due_date' => $application->payment_due_date ?? 16,
        'status' => 'active',
        'remarks' => 'Auto-generated from approved application'
    ]);

    // create user login
    $password = $application->unit->unit_code . $application->last_name;

    $user = User::create([
        'email' => $tenant->email,
        'name' => $tenant->first_name . ' ' . $tenant->last_name,
        'password' => Hash::make($password),
        'role' => 'tenant',
    ]);

    // send email
    Mail::to($user->email)->send(new TenantMail($user->email, $password));

    return response()->json([
        'message' => 'Application confirmed and tenant + contract created!',
        'tenant' => $tenant,
        'contract' => $contract,
        'user' => $user
    ]);
}

public function indexView()
{
    $applications = Application::with('unit')->get();
    return view('admin.applications', compact('applications'));
}

public function reject($id)
{
    $application = Application::findOrFail($id);
    $application->status = 'Rejected';
    $application->save();

    return redirect()->back()->with('message', 'Application has been rejected.');
}




}
