<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // <-- IDAGDAG ITO

class TenantController extends Controller
{
    /**
     * Display the tenant's main dashboard (home).
     */
    public function home()
    {
        // 1. Kunin ang logged-in user at i-load lahat ng related data
        $user = Auth::user()->load(
            'tenant.unit', 
            'tenant.contracts', 
            'tenant.payments'
        );

        if (!$user->tenant) {
            abort(404, 'Tenant record not found for this user.');
        }

        // 2. Hanapin ang active contract
        $activeContract = $user->tenant->contracts
                            ->whereIn('status', ['active', 'ongoing'])
                            ->first();

        // 3. Kunin ang 3 pinakabagong payments
        $payments = $user->tenant->payments
                        ->sortByDesc('payment_date')
                        ->take(3);

        // 4. Kalkulahin ang next due date
        $nextDueDate = null;
        if ($activeContract) {
            $today = Carbon::now();
            $dueDate = $activeContract->payment_due_date;
            
            if ($today->day > $dueDate) {
                $nextDueDate = $today->addMonth()->day($dueDate)->format('M d, Y');
            } else {
                $nextDueDate = $today->day($dueDate)->format('M d, Y');
            }
        }

        // 5. Ipasa lahat ng data sa view
        return view('tenant.home', [ // 'tenant.home' ang ginamit mo sa function mo
            'tenant' => $user, // Ang blade file ay gumagamit ng $tenant->name
            'activeContract' => $activeContract,
            'payments' => $payments,
            'nextDueDate' => $nextDueDate
        ]);
    }

    public function property()
    {
        // Kailangan din nitong i-load ang data
        $user = Auth::user()->load('tenant.unit', 'tenant.contracts');
        
        if (!$user->tenant) {
            abort(404, 'Tenant record not found.');
        }

        $activeContract = $user->tenant->contracts
                            ->whereIn('status', ['active', 'ongoing'])
                            ->first();

        return view('tenant.property', [
            'tenant' => $user,
            'contract' => $activeContract,
            'unit' => $user->tenant->unit
        ]);
    }

    /**
     * Display the tenant's payment history page.
     */
    public function payments()
    {
        $user = Auth::user()->load('tenant.contracts', 'tenant.payments');

        if (!$user->tenant) {
            abort(404, 'Tenant record not found.');
        }

        // Kunin lahat ng payments, pinakabago muna
        $payments = $user->tenant->payments->sortByDesc('payment_date');

        // Kunin ang active contract para sa due date at amount
        $activeContract = $user->tenant->contracts
                            ->whereIn('status', ['active', 'ongoing'])
                            ->first();

        $balance = 0;
        $dueDate = 'N/A';

        if ($activeContract) {
            // Ang "balance" dito ay ang susunod na bayarin
            $balance = $activeContract->monthly_payment;
            
            // Kalkulahin ang next due date
            $today = Carbon::now();
            $dueDay = $activeContract->payment_due_date;
            if ($today->day > $dueDay) {
                $dueDate = $today->addMonth()->day($dueDay)->format('M d, Y');
            } else {
                $dueDate = $today->day($dueDay)->format('M d, Y');
            }
        }

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

    // --- MGA ADMIN API FUNCTIONS MO (HINDI KO GINALAW) ---

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
            'last_name'=> 'required|string|max:100',
            'email'=> 'required|email|unique:tenants,email,' . $tenant->id,
            'contact_num'=> 'required|string|max:50',
            'unit_id'=> 'required|exists:units,id',
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
}