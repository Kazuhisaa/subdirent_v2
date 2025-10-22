<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; 
// Wala na 'yung PayMongo at Payment model dito

class TenantController extends Controller
{
    //===========================================================
    // TENANT WEB ROUTES (Para sa naka-login na tenant)
    //===========================================================

    public function home()
    {
        $user = Auth::user()->load('tenant.unit', 'tenant.contracts', 'tenant.payments');
        if (!$user->tenant) { abort(404, 'Tenant record not found.'); }

        $activeContract = $user->tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();
        $payments = $user->tenant->payments->sortByDesc('payment_date')->take(3);
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

        return view('tenant.home', [
            'tenant' => $user, 
            'activeContract' => $activeContract,
            'payments' => $payments,
            'nextDueDate' => $nextDueDate
        ]);
    }

    public function property()
    {
        $user = Auth::user()->load('tenant.unit', 'tenant.contracts');
        if (!$user->tenant) { abort(404, 'Tenant record not found.'); }
        $activeContract = $user->tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();
        return view('tenant.property', [
            'tenant' => $user,
            'contract' => $activeContract,
            'unit' => $user->tenant->unit
        ]);
    }

    public function payments()
    {
        $user = Auth::user()->load('tenant.contracts', 'tenant.payments');
        if (!$user->tenant) { abort(404, 'Tenant record not found.'); }
        $tenant = $user->tenant;
        $payments = $tenant->payments->sortByDesc('payment_date');
        $activeContract = $tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();
        $outstanding = 0;
        $nextMonth = ['date' => 'N/A', 'for_month' => null];

        if ($activeContract) {
            $outstanding = $activeContract->monthly_payment; 
            $dueDay = $activeContract->payment_due_date;
            $nextDueDateCarbon = Carbon::now()->day($dueDay);
            if (Carbon::now()->day > $dueDay) {
                $nextDueDateCarbon->addMonth();
            } 
            $nextMonth['date'] = $nextDueDateCarbon->format('M d, Y');
            $nextMonth['for_month'] = $nextDueDateCarbon->format('Y-m-d');
        }

        return view('tenant.payments', [
            'tenant' => $tenant,
            'outstanding' => $outstanding,
            'nextMonth' => $nextMonth,
            'payments' => $payments,
            'activeContract' => $activeContract
        ]);
    }

        public function maintenance()
    {
        return view('tenant.maintenance');
    }

        public function propertysearch()
    {
        $user = Auth::user()->load('tenant.unit', 'tenant.contracts');

        // Safety check: ensure this user has a tenant record
        if (!$user->tenant) {
            abort(404, 'Tenant record not found.');
        }

        $tenant = $user->tenant;

        // Optional: get only active/ongoing contract
        $activeContract = $tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();

        return view('tenant.propertysearch', [
            'tenant' => $user,
            'contract' => $activeContract,
            'unit' => $tenant->unit
        ]);
    }

        public function account()
    {
        $user = Auth::user()->load('tenant.unit');
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(404, 'Tenant record not found.');
        }

        return view('tenant.account', [
            'tenant' => $user, // includes ->tenant->unit
        ]);
    }

        public function updatecredentials(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Login credentials updated successfully.');
    }

        public function accountupdate(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $tenant->update($validated);

        return redirect()->back()->with('success', 'Account updated successfully.');
    }


    public function ledger()
    {
        return view('tenant.ledger'); // optional
    }

    //===========================================================
    // ADMIN API ROUTES (Sanctum Protected)
    //===========================================================

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