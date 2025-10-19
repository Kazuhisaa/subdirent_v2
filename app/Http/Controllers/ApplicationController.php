<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Payment;
use App\Mail\TenantMail;
use App\Models\Contract;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    // List all applications (API)
    public function index()
    {
        $application = Application::with('unit')->get();
        return response()->json($application);
    }

    // Show a specific application
    public function show($id)
    {
        $application = Application::with('unit')->where('id', $id)->get();
        return response()->json($application);
    }

    // Store new application
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'first_name'   => 'required|string|max:50',
            'middle_name'  => 'nullable|string|max:50',
            'last_name'    => 'required|string|max:50',
            'email'        => 'required|email|unique:applications,email',
            'contact_num'  => 'required|string|max:15',
            'monthly_rent' => 'nullable|numeric',
            'unit_price'   => 'nullable|numeric',
            'downpayment'  => 'nullable|numeric',
            'payment_due_date' => 'nullable|integer|min:1|max:31',
            'contract_years' => 'nullable|integer|min:1',
            'contract_start' => 'nullable|date',
            'contract_duration' => 'nullable|numeric|max:20',
            'remarks' => 'nullable|string',
        ]);

        $application = Application::create($credentials);

        return response()->json([
            'message' => 'Application successfully created',
            'data' => $application
        ], 200);
    }

    // Update application
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
            'unit_price' => 'nullable|numeric', // Hayaan mo lang ito
            'downpayment' => 'nullable|numeric',
            'payment_due_date' => 'nullable|integer|min:1|max:31',
            'contract_years' => 'nullable|integer|min:1',
            'contract_duration' => 'nullable|numeric|max:20',
            'contract_start' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $application = Application::findOrFail($id);
        
        // Kunin ang unit
        $unit = Unit::find($request->unit_id);

        // ILAGAY ANG TAMANG PRICE SA LOOB NG $credentials
        if ($unit) {
            // Dito, ino-overwrite natin yung 'null' sa $credentials
            // ng tamang presyo galing sa unit.
            $credentials['unit_price'] = (float) str_replace(',', '', $unit->unit_price);
            
            // Baka gusto mo rin isama ang monthly_rent?
            // Kung ang monthly_rent ay galing din sa unit at hindi sa form:
            // $credentials['monthly_rent'] = (float) str_replace(',', '', $unit->monthly_rent);
        }

        // Ngayon, kapag tinawag ang update, dala na ng $credentials ang tamang presyo.
        $application->update($credentials);

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => $application
        ], 200);
    }

    // Approve application and create tenant + contract
  public function approve($id)
    {
        // Na-load na natin ang 'unit' relationship
        $application = Application::with('unit')->findOrFail($id);

        // Validate necessary fields
        if (!$application->downpayment || !$application->unit_price || !$application->contract_years) {
            return response()->json([
                'error' => 'Please complete Unit Price, Downpayment, and Contract Years before confirming.'
            ], 422);
        }

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

        if ($application->unit) { 
            $application->unit->status = 'Rented';
            $application->unit->save();
        }
        // --- SIMULA NG BAGONG CALCULATION LOGIC ---

        // 1. Kunin ang mga base values
        $original_unit_price = $application->unit_price;
        $downpayment = $application->downpayment;
        $durationYears = $application->contract_years; // Galing na sa application
        $total_months = $durationYears * 12;

        $principal_amount = $original_unit_price - $downpayment;

        // Ito yung (principal * 1.08)
        $total_price_with_interest = $principal_amount * 1.08;

        // 4. Kalkulahin ang Raw Monthly Payment (na may butal)
        // Dito, hinati natin ang total amount sa total months
        $raw_monthly_payment = $total_price_with_interest / $total_months;

        // 5. I-round UP sa pinakamalapit na 500 (based sa example mo na 16234 -> 16500)
        // Ginagamit natin ang ceil() para laging "UP"
        $rounded_monthly_payment = ceil($raw_monthly_payment / 500) * 500;

        // --- WAKAS NG BAGONG CALCULATION LOGIC ---


        // Determine contract start and end
        $startDate = $application->contract_start
            ? Carbon::parse($application->contract_start)
            : Carbon::now();
        
        $endDate = $startDate->copy()->addYears($durationYears);

        // Create contract
        $contract = Contract::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $application->unit_id,
            'contract_start' => $startDate,
            'contract_end' => $endDate,
            'contract_duration' => $durationYears,
            'unit_price' => $original_unit_price,       // <-- REQUIREMENT 1 (Original Price)
            'downpayment' => $downpayment,
            'total_price' => $total_price_with_interest, // <-- Total Financed Amount (with interest)
            'monthly_payment' => $rounded_monthly_payment, // <-- REQUIREMENT 3 & 4 (Calculated & Rounded)
            'payment_due_date' => $application->payment_due_date ?? 16,
            'status' => 'ongoing',
            'remarks' => 'Auto-generated from approved application'
        ]);

        // --- SIMULA NG PAYMENT LOGIC (Downpayment) ---
        
        $reference_no = 'DP-' . str_pad($application->id, 6, '0', STR_PAD_LEFT);
        $invoice_no = 'INV-DP-' . str_pad($application->id, 6, '0', STR_PAD_LEFT);

        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'contract_id' => $contract->id,
            'amount' => $application->downpayment,
            'payment_method' => 'Initial Deposit', 
            'payment_status' => 'Paid', 
            'payment_date' => Carbon::now(),
            'reference_no' => $reference_no,
            'invoice_no' => $invoice_no,
            'invoice_pdf' => null, 
            'remarks' => 'Downpayment' 
        ]);
        
        // --- SIMULA NG USER LOGIC ---

        $password = $application->unit->unit_code . $application->last_name;
        $user = User::create([
            'email' => $tenant->email,
            'name' => $tenant->first_name . ' ' . $tenant->last_name,
            'password' => Hash::make($password),
            'role' => 'tenant',
        ]);

        // Send email with credentials
        Mail::to($user->email)->send(new TenantMail($user->email, $password));

        // --- WAKAS ---

        return response()->json([
            'message' => 'Application approved and tenant + contract created!',
            'tenant' => $tenant,
            'contract' => $contract,
            'user' => $user,
            'payment' => $payment 
        ]);
    }

    // Archive application
    public function archive($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return response()->json([
            'message' => 'Application archived successfully',
            'data' => $application
        ]);
    }

    // View archived applications
    public function viewArchive()
    {
        $archived = Application::onlyTrashed()->get();
        return response()->json($archived);
    }

    // Reject application
    public function reject($id)
    {
        $application = Application::findOrFail($id);
        $application->status = 'Rejected';
        $application->save();

        return redirect()->back()->with('message', 'Application has been rejected.');
    }

    // Admin view of applications
    public function indexView()
    {
        $applications = Application::with('unit')->get();
        return view('admin.applications', compact('applications'));
    }
}
