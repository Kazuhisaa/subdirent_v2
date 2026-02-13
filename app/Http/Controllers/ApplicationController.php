<?php

namespace App\Http\Controllers;


use App\Models\{
    Unit,
    User,
    Tenant,
    Payment,
    Contract,
    Application
};
use App\Mail\TenantMail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Hash, Mail};
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\RevenueService;
use Illuminate\Support\Facades\Storage;



class ApplicationController extends Controller
{
    /**
     * List all applications (API)
     */

     //pang connect sa service
    protected $revenueservice;

    public function __construct(RevenueService $revenueservice){
         $this->revenueservice = $revenueservice;

    }

    public function index()
    {
        $applications = Application::with('unit')->get();

        return response()->json($applications);
    }

    /**
     * Show a specific application
     */
  public function show($id)
{
    $application = Application::with('unit')->findOrFail($id);

    return response()->json([
        'application' => $application->toArray()
    ]);
}

    /**
     * Store new application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id'           => 'required|exists:units,id',
            'first_name'        => 'required|string|max:50',
            'middle_name'       => 'nullable|string|max:50',
            'last_name'         => 'required|string|max:50',
            'email'             => 'required|email|unique:applications,email',
            'contact_num'       => 'required|string|max:15',
            'monthly_rent'      => 'nullable|numeric',
            'unit_price'        => 'nullable|numeric',
            'downpayment'       => 'nullable|numeric',
            'payment_due_date'  => 'nullable|integer|min:1|max:31',
            'contract_years'    => 'nullable|integer|min:1',
            'contract_start'    => 'nullable|date',
            'contract_duration' => 'nullable|numeric|max:20',
            'remarks'           => 'nullable|string',
        ]);

        // Optional: auto-set unit price if not given
        if (empty($validated['unit_price'])) {
            $unit = Unit::find($validated['unit_id']);
            if ($unit) {
                $validated['unit_price'] = $unit->unit_price;
            }
        }
     $fromDate = Carbon::now()->startOfMonth()->toDateString();
        $application = Application::create($validated);
       $updatedCount = $this->revenueservice->addnewContract($fromDate,1);
        return response()->json([
            'message' => 'Application successfully created',
            'data'    => $application,
              //  'updatedCount' => $updatedCount
        ], 201);
    }

   public function update(Request $request, $id)
{
    $validated = $request->validate([
        'unit_id'           => 'required|exists:units,id',
        'first_name'        => 'required|string|max:50',
        'middle_name'       => 'nullable|string|max:50',
        'last_name'         => 'required|string|max:50',
        'email'             => 'required|email|unique:applications,email,' . $id,
        'contact_num'       => 'required|string|max:15',
        'monthly_rent'      => 'nullable|numeric',
        'unit_price'        => 'nullable|numeric',
        'downpayment'       => 'nullable|numeric',
        'payment_due_date'  => 'nullable|integer|min:1|max:31',
        'contract_years'    => 'nullable|integer|min:1',
        'contract_duration' => 'nullable|numeric|max:20',
        'contract_start'    => 'nullable|date',
        'remarks'           => 'nullable|string',
    ]);

    $application = Application::findOrFail($id);
    $unit = Unit::find($validated['unit_id']);

    if ($unit) {
        $validated['unit_price'] = (float) str_replace(',', '', $unit->unit_price);
    }

    // Check that downpayment is not greater than unit_price
    if (isset($validated['downpayment']) && $validated['downpayment'] > $validated['unit_price']) {
        return response()->json([
            'message' => 'Downpayment cannot be greater than unit price'
        ], 422);
    }

    $application->update($validated);

    return response()->json([
        'message'     => 'Application updated successfully',
        'application' => $application
    ]);
}


    /**
     * Approve an application → create Tenant + Contract + Payment + User
     */
    public function approve($id)
    {
        $application = Application::with('unit')->findOrFail($id);

        if (!$application->downpayment || !$application->unit_price || !$application->contract_years) {
            return response()->json([
                'error' => 'Please complete Unit Price, Downpayment, and Contract Years before confirming.'
            ], 422);
        }

        $application->update(['status' => 'Approved']);

    // --- Create User Account ---
        $password = $application->unit->unit_code . $application->last_name;
        $user = User::create([
            'email'    => $application->email,
            'name'     => $application->first_name . ' ' . $application->last_name,
            'password' => Hash::make($password),
            'role'     => 'tenant',
        ]);


        // --- Create Tenant ---
        $tenant = Tenant::create([
            'first_name'  => $application->first_name,
            'middle_name' => $application->middle_name,
            'last_name'   => $application->last_name,
            'email'       => $application->email,
            'contact_num' => $application->contact_num,
            'unit_id'     => $application->unit_id,
             'user_id'  => $user->id,
        ]);

        // Update unit status
        if ($application->unit) {
            $application->unit->update(['status' => 'Rented']);
        }

        // --- Payment and Contract Calculations ---
        $unit_price     = $application->unit_price;
        $downpayment    = $application->downpayment;
        $years          = $application->contract_years;
        $total_months   = $years * 12;

        $principal      = $unit_price - $downpayment;
        $with_interest  = $principal * 1.08;
        $raw_payment    = $with_interest / $total_months;
        $monthly_payment = ceil($raw_payment / 500) * 500;

        // --- Contract Duration ---
        $start_date = $application->contract_start
            ? Carbon::parse($application->contract_start)
            : Carbon::now();

        $end_date = $start_date->copy()->addYears($years);

        // --- Create Contract ---
        $contract = Contract::create([
            'tenant_id'        => $tenant->id,
            'unit_id'          => $application->unit_id,
            'contract_start'   => $start_date,
            'contract_end'     => $end_date,
            'contract_duration'=> $years,
            'unit_price'       => $unit_price,
            'downpayment'      => $downpayment,
            'total_price'      => $with_interest,
            'monthly_payment'  => $monthly_payment,
            'payment_due_date' => $application->payment_due_date ?? 16,
            'status'           => 'ongoing',
            'remarks'          => 'Auto-generated from approved application',
        ]);

        // --- Create Payment Record (Downpayment) ---
        $payment = Payment::create([
            'tenant_id'      => $tenant->id,
            'contract_id'    => $contract->id,
            'amount'         => $downpayment,
            'payment_method' => 'Initial Deposit',
            'payment_status' => 'Paid',
            'payment_date'   => now(),
            'for_month'   => now(),
            'reference_no'   => 'DP-' . str_pad($application->id, 6, '0', STR_PAD_LEFT),
            'invoice_no'     => 'INV-DP-' . str_pad($application->id, 6, '0', STR_PAD_LEFT),
            'remarks'        => 'Downpayment'
        ]);

    

        // Send email credentials
        Mail::to($user->email)->send(new TenantMail($user->email, $password));

// ✅ Generate contract PDF (AFTER all data is ready)
$pdf = Pdf::loadView('tenant.contract', [
    'contract' => $contract,
    'tenant'   => $contract->tenant,
    'unit'     => $contract->unit,
]);

$fileName = 'contract_' . $contract->tenant->last_name . '_' . now()->format('YmdHis') . '.pdf';
$filePath = 'contracts/' . $fileName;
Storage::disk('public')->put($filePath, $pdf->output());

// ✅ Update DB with PDF path
$contract->update(['contract_pdf' => $filePath]);

return redirect()
    ->route('admin.applications')
    ->with('success', 'Application approved, user created, and contract PDF generated successfully!');

    }

    /**
     * Archive (soft delete) an application
     */
public function archive($id)
    {
        try {
            // Use find() instead of findOrFail() to prevent 404 errors
            $application = Application::find($id);

            // Check if it exists before trying to delete
            if ($application) {
                $application->delete();

                // ✅ Return a JSON response for the JavaScript
                return response()->json([
                    'message' => 'Application archived successfully!'
                ], 200);
            }

            // If it's already gone, just say it was successful
            return response()->json([
                'message' => 'Application was already archived.'
            ], 200);

        } catch (\Exception $e) {
            // ✅ Catch any other potential errors
            return response()->json([
                'message' => 'Error archiving applicant: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * View archived applications
     */
    public function viewArchive()
    {
        $archived = Application::onlyTrashed()->with('unit')->get();

        return response()->json($archived);
    }

    /** z`
     * Reject application
     */
    public function reject($id)
    {
        $application = Application::findOrFail($id);
        $application->update(['status' => 'Rejected']);

        return redirect()->back()->with('message', 'Application has been rejected.');
    }

    /**
     * Admin view for web dashboard
     */
    public function indexView()
    {
        // Idagdag lang ang ->latest() bago ang ->get()
$applications = Application::orderByRaw("
        CASE
            WHEN status = 'Pending' THEN 1
            WHEN status = 'Approved' THEN 2
            WHEN status = 'Rejected' THEN 3
            ELSE 4
        END")
    ->latest() // This keeps your original date sorting as a secondary rule
    ->paginate(10);
            $units = Unit::all();


        return view('admin.applications', compact('applications', 'units'));
    }

    public function showContract($id)
{
    $contract = Contract::findOrFail($id);
    if (!$contract->contract_pdf || !Storage::disk('public')->exists($contract->contract_pdf)) {
        abort(404, 'Contract PDF not found.');
    }
    return response()->file(storage_path('app/public/' . $contract->contract_pdf));
}

public function restore($id)
    {
            // Find the trashed application by its ID
            $application = Application::onlyTrashed()->findOrFail($id);
            // Restore the application
            $application->restore();

            // Return a JSON response on success
            return response()->json([
                'message' => 'Applicant restored successfully!',
                'data' => $application
    ]);
    }

}
