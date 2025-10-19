<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function home()
{
    $tenant = auth()->user()->tenant()->with('unit')->first();
    return view('tenant.home', compact('tenant'));
}

public function property()
{
    $tenant = auth()->user()->tenant()->with('unit')->first();
    return view('tenant.property', compact('tenant'));
}
        

    public function index(Request $request){    

        if (!$request->user() || !$request->user()->tokenCan('admin')) {
        return response()->json(['message' => 'Unauthorized'], 403);
        }


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

    public function payments()
{
    // Example dummy data for now
    $balance = 200.00;
    $dueDate = 'December 11, 2025';
    $payments = [
        ['date' => '10/10/2025', 'confirmation' => 'A1B2-C3D4', 'amount' => 2000.00],
        ['date' => '09/09/2025', 'confirmation' => 'F5G6-H7I8', 'amount' => 2000.00],
        ['date' => '08/08/2025', 'confirmation' => 'J9K1-L2M3', 'amount' => 2000.00],
    ];

    return view('tenant.payments', compact('balance', 'dueDate', 'payments'));
}

public function makePayment(Request $request)
{
    // Example: payment logic here (Stripe, PayPal, etc.)
    // For now, just simulate success
    return back()->with('status', 'Payment processed successfully!');
}

public function ledger()
{
    return view('tenant.ledger'); // optional
}


    

}


