<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Models\Maintenance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

// Wala na 'yung PayMongo at Payment model dito

class TenantController extends Controller
{
    // ===========================================================
    // TENANT WEB ROUTES (Para sa naka-login na tenant)
    // ===========================================================

   public function home()
    {
        $user = Auth::user()->load('tenant.unit', 'tenant.contracts', 'tenant.payments');
        if (! $user->tenant) {
            abort(404, 'Tenant record not found.');
        }

        $tenant = $user->tenant;
        $activeContract = $tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();
        $payments = $tenant->payments;

        $nextUnpaidDueDate = null;
        $calendarEvents = []; // This will hold payment events
        $recentPayments = $payments->sortByDesc('payment_date')->take(5);

        // GET DYNAMIC MAINTENANCE COUNTS
        // We use the statuses from maintenance.blade.php (Pending, In Progress, Completed)
        $maintenanceStats = Maintenance::where('tenant_id', $tenant->id)
            ->selectRaw("
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as inprogress,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
            ")
            ->first();
        
        // Prepare the counts for the view, defaulting to 0
        $maintenanceCounts = [
            'pending' => $maintenanceStats->pending ?? 0,
            'inprogress' => $maintenanceStats->inprogress ?? 0,
            'completed' => $maintenanceStats->completed ?? 0,
        ];

        // === START NEW SECTION ===
        $maintenanceRequests = Maintenance::where('tenant_id', $tenant->id)
            // --- CHANGE: Use FIELD to prioritize statuses ---
            ->orderByRaw("FIELD(status, 'In Progress', 'Pending', 'Completed')")
            // --- Then, order by newest requests within each group ---
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 2. GET DATA FOR THE CALENDAR (UNLIMITED, focused on scheduled future/ongoing events)
        $calendarMaintenanceRequests = Maintenance::where('tenant_id', $tenant->id)
            ->where('status', 'In Progress')
            ->whereNotNull('scheduled_date')
            ->get(); 

        $maintenanceEvents = [];
        foreach ($calendarMaintenanceRequests as $request) {
            $maintenanceEvents[] = [
                'title' => 'Service: ' . $request->category,
                'start' => Carbon::parse($request->scheduled_date)->format('Y-m-d'),
                'color' => '#6f42c1', // Bootstrap Purple
                'description' => $request->description
            ];
        }
        
        // === END NEW SECTION ===


        if ($activeContract) {
            $monthlyRent = $activeContract->monthly_payment ?? 0;
            $billingDay = $activeContract->payment_due_date ?? 1;
            $contractEnd = Carbon::parse($activeContract->contract_end);

            // Fetch all payments for this contract/tenant
            $rentPayments = Payment::where('tenant_id', $tenant->id)
                ->where('contract_id', $activeContract->id)
                ->whereIn('payment_status', ['paid', 'partial'])
                ->orderBy('for_month', 'asc')
                ->get();

            $paymentsByMonth = $rentPayments->groupBy(function($payment) {
                return Carbon::parse($payment->for_month)->format('Y-m');
            });

            // Determine Next Unpaid Due Date & Build Events
            $currentDate = Carbon::parse($activeContract->contract_start)->day($billingDay);
            $foundUnpaid = false;

            while ($currentDate->lessThanOrEqualTo($contractEnd)) {
                $monthKey = $currentDate->format('Y-m');
                $totalPaid = $paymentsByMonth->get($monthKey, collect())->sum('amount');
                $isPaid = $totalPaid >= $monthlyRent;

                // Calendar Event Creation
                $eventStatus = '';
                $eventColor = '';

                if ($isPaid) {
                    $eventStatus = 'Paid';
                    $eventColor = '#1B5F99'; // Blue-600 from tenant.css
                } elseif ($totalPaid > 0 && $totalPaid < $monthlyRent) {
                    $eventStatus = 'Partial';
                    $eventColor = '#ff7043'; // Orange for Partial
                } else {
                    $eventStatus = 'Due';
                    $eventColor = '#dc3545'; // Red for Unpaid/Due
                }

                $calendarEvents[] = [
                    'title' => $eventStatus . ' - ₱' . number_format($monthlyRent, 2),
                    'start' => $currentDate->format('Y-m-d'),
                    'color' => $eventColor,
                ];

                // Find Next Unpaid Date
                if (!$foundUnpaid && $currentDate->greaterThanOrEqualTo(Carbon::now()->subDay()) && $totalPaid < $monthlyRent) {
                    $nextUnpaidDueDate = $currentDate->copy()->format('M d, Y');
                    $foundUnpaid = true;
                }
                
                // If it's fully paid or the month is far in the past, move to the next month
                $currentDate->addMonth();
            }

            // If $nextUnpaidDueDate is still null, set it to the next official due date for the current month or next month
            if (!$nextUnpaidDueDate) {
                $today = Carbon::now();
                if ($today->day > $billingDay) {
                    $nextUnpaidDueDate = $today->addMonth()->day($billingDay)->format('M d, Y');
                } else {
                    $nextUnpaidDueDate = $today->day($billingDay)->format('M d, Y');
                }
            }

        }

         $recentInvoices = Payment::where('tenant_id', $user->tenant->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        // === START MODIFIED SECTION ===
        // Merge payment and maintenance events for the calendar
        $allCalendarEvents = array_merge($calendarEvents, $maintenanceEvents);

        return view('tenant.home', [
            'tenant' => $user,
            'activeContract' => $activeContract,
            'recentPayments' => $recentPayments,
            'nextUnpaidDueDate' => $nextUnpaidDueDate,
            'calendarEvents' => json_encode($allCalendarEvents), // Pass all events
            'maintenanceCounts' => $maintenanceCounts,
            'maintenanceRequests' => $maintenanceRequests, // Pass full list for new table
        ]);
        // === END MODIFIED SECTION ===
    }

public function property()
{
    $user = Auth::user()->load('tenant.unit', 'tenant.contracts');

    if (! $user->tenant) {
        abort(404, 'Tenant record not found.');
    }

    $activeContract = $user->tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();

    $unit = $user->tenant->unit;
    $predictions = [];

    if ($unit) {
        $data = [
            'bathroom' => $unit->bathroom,
            'bedroom' => $unit->bedroom,
            'floor_area' => $unit->floor_area,
            'lot_size' => $unit->lot_size,
            'year' => date('Y'),
            'n_years' => 5
        ];

        $response = Http::post('http://127.0.0.1:5000/predict', $data);

        if ($response->successful()) {
           $predictions = $response->json();
        } else {
            Log::error('Prediction API error: ' . $response->body());
        }
    }

    return view('tenant.property', [
        'tenant' => $user,
        'contract' => $activeContract,
        'unit' => $unit,
        'predictions' => $predictions, // ✅ pass predictions to Blade
    ]);
}


public function payments()
{
    $user = Auth::user()->load('tenant.contracts');
    if (!$user->tenant) {
        abort(404, 'Tenant record not found.');
    }

    $tenant = $user->tenant;
    $activeContract = $tenant->contracts
        ->whereIn('status', ['active', 'ongoing'])
        ->first();

    if (!$activeContract) {
        return back()->with('error', 'No active contract found.');
    }

    $monthlyRent = $activeContract->monthly_payment ?? 0;
    $billingDay = $activeContract->payment_due_date ?? 1;

    // Kunin lahat ng payments (partial & full)
    $payments = Payment::where('tenant_id', $tenant->id)
        ->where('contract_id', $activeContract->id)
        ->whereIn('payment_status', ['paid', 'partial'])
        ->orderBy('for_month', 'asc')
        ->get();

    // Simula month = next month pagkatapos ng downpayment
    $downpayment = $payments->where('remarks', 'Downpayment')->first();
    $currentMonth = $downpayment
        ? Carbon::parse($downpayment->for_month)->copy()->addMonth()
        : Carbon::parse($activeContract->start_date);

    $currentMonth->day($billingDay);

    $outstanding = $monthlyRent;

    // Hanapin month na hindi pa fully paid
    $paymentsByMonth = $payments->groupBy(function($payment) {
        return Carbon::parse($payment->for_month)->format('Y-m');
    });

    foreach ($paymentsByMonth as $month => $monthPayments) {
        $yearMonth = Carbon::parse($month . '-01');
        $totalPaid = Payment::where('tenant_id', $tenant->id)
            ->where('contract_id', $activeContract->id)
            ->whereYear('for_month', $yearMonth->year)
            ->whereMonth('for_month', $yearMonth->month)
            ->sum('amount');

        if ($totalPaid < $monthlyRent) {
            $outstanding = $monthlyRent - $totalPaid;
            $currentMonth = $yearMonth->copy()->day($billingDay);
            break;
        } else {
            $currentMonth = $yearMonth->copy()->addMonth()->day($billingDay);
        }
    }

    // Penalty kung overdue sa current month lang
    $now = Carbon::now();
    $penalty = 0;
    if ($now->year == $currentMonth->year && $now->month == $currentMonth->month && $now->day > $billingDay && $outstanding > 0) {
        $daysLate = $now->day - $billingDay;
        if ($daysLate > 5) {
            $penalty = $monthlyRent * 0.02;
            $outstanding += $penalty;
        }
    }

    $nextMonth = [
        'for_month' => $currentMonth->format('Y-m-d'),
        'date' => $currentMonth->format('M d, Y'),
    ];

    $amountToPay = $outstanding;

    // Determine payment status for current and next month dynamically
    $currentDate = Carbon::now();
    $currentMonthKey = $currentDate->format('Y-m');
    $nextMonthKey = $currentDate->copy()->addMonth()->format('Y-m');

    // Check if current and next months are paid in full
    $isCurrentMonthPaid = false;
    $isNextMonthPaid = false;

    foreach ($paymentsByMonth as $month => $monthPayments) {
        $totalPaid = $monthPayments->sum('amount');

        if ($month === $currentMonthKey && $totalPaid >= $monthlyRent) {
            $isCurrentMonthPaid = true;
        }
        if ($month === $nextMonthKey && $totalPaid >= $monthlyRent) {
            $isNextMonthPaid = true;
        }
    }

    // For extra clarity: detect if user has prepaid beyond next month
    $prepaidMonths = collect($paymentsByMonth)
        ->filter(fn($monthPayments) => $monthPayments->sum('amount') >= $monthlyRent)
        ->keys()
        ->filter(fn($month) => Carbon::parse($month . '-01')->greaterThan($currentDate))
        ->count();

    $paymentStatus = [
        'currentMonthPaid' => $isCurrentMonthPaid,
        'nextMonthPaid' => $isNextMonthPaid,
        'prepaidMonths' => $prepaidMonths,
        'currentMonth' => $currentDate->format('F'),
        'nextMonth' => $currentDate->copy()->addMonth()->format('F'),
    ];

    return view('tenant.payments', compact(
        'tenant',
        'activeContract',
        'payments',
        'nextMonth',
        'outstanding',
        'penalty',
        'amountToPay',
        'paymentStatus',
    ));
}

    public function maintenance()
    {
        return view('tenant.maintenance');
    }

    public function propertysearch()
    {
        $user = Auth::user()->load('tenant.unit', 'tenant.contracts');

        // Safety check: ensure this user has a tenant record
        if (! $user->tenant) {
            abort(404, 'Tenant record not found.');
        }

        $tenant = $user->tenant;

        // Optional: get only active/ongoing contract
        $activeContract = $tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();

        return view('tenant.propertysearch', [
            'tenant' => $user,
            'contract' => $activeContract,
            'unit' => $tenant->unit,
        ]);
    }

    public function account()
    {
        $user = Auth::user()->load('tenant.unit');
        $tenant = $user->tenant;

        if (! $tenant) {
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
            'email' => 'required|email|unique:users,email,'.$user->id,
            // === MODIFIED: Added max rule ===
            'password' => 'nullable|confirmed|min:8|max:72',
        ]);

        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
            // === NEW: Set default password flag to false ===
            $user->is_password_default = false;
        }

        $user->save();

        return redirect()->back()->with('success', 'Login credentials updated successfully.');
    }

    public function accountupdate(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (! $tenant) {
            abort(404, 'Tenant not found.');
        }

        // 3. VALIDATE PERSONAL INFO + NEW AVATAR
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_num' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Avatar validation
        ]);

        // 4. HANDLE THE AVATAR UPLOAD
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $uploadPath = public_path('uploads/tenants/'); // Your requested path
            
            // Create directory if it doesn't exist
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Create a unique filename
            $filename = 'tenant-' . $tenant->id . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'uploads/tenants/' . $filename; // Path to save in DB

            // Move the new file
            $file->move($uploadPath, $filename);

            // Delete the old avatar if it exists
            if ($user->profile_photo_path && File::exists(public_path($user->profile_photo_path))) {
                File::delete(public_path($user->profile_photo_path));
            }

            // Update the user's photo path
            $user->profile_photo_path = $path;
            $user->save();
        }

        // 5. UPDATE TENANT'S PERSONAL INFO
        // Remove 'avatar' from validated data before updating tenant
        unset($validated['avatar']); 
        $tenant->update($validated);

        return redirect()->back()->with('success', 'Account updated successfully.');
    }

    public function ledger($tenant_id)
    {
        $payments = \App\Models\Payment::where('tenant_id', $tenant_id)
            ->orderBy('payment_date', 'desc')
            ->get();

    }
    // ===========================================================
    // ADMIN API ROUTES (Sanctum Protected)
    // ===========================================================

    public function index(Request $request)
    {
        if (! $request->user() || ! $request->user()->tokenCan('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $application = Tenant::with('unit')->get();

        return response()->json($application);
    }

    public function show($id)
    {
        $application = Tenant::with('unit')->where('id', $id)->get();

        return response()->json($application);
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:tenants,email,'.$tenant->id,
            'contact_num' => 'required|string|max:50',
            'unit_id' => 'required|exists:units,id',
        ]);
        $tenant->update($validated);

        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => $tenant,
        ]);
    }

    public function archive($id)
    {
        $application = Tenant::findOrFail($id);
        $application->delete();

        return response()->json([
            'Message' => 'Tenant Archived Successfully',
            'data' => $application,
        ]);
    }

    public function viewArchive()
    {
        $tenants = Tenant::onlyTrashed()->with('unit')->get();

        return response()->json($tenants);    
    }

    public function restore($id)
{
    $tenant = Tenant::onlyTrashed()->findOrFail($id);
    $tenant->restore();

    return response()->json([
        'message' => 'Tenant restored successfully',
        'tenant' => $tenant,
    ]);
}


public function autopaySetup(Request $request)
{
    $request->validate([
        'payment_method' => 'required|string|max:255',
    ]);

    // Example: store payment method setup for tenant
    $tenant = auth()->user()->tenant;

    // You can later integrate Stripe API here
    $tenant->update([
        'autopay_method' => $request->payment_method,
        'autopay_active' => true,
    ]);

    return back()->with('autopay_status', 'Autopay has been activated successfully!');
}



public function showPayments($tenantId)
{
    $tenant = Tenant::with('autopay')->findOrFail($tenantId);
    $contract = Contract::where('tenant_id', $tenantId)->first();

    return view('tenant.payments', compact('tenant', 'contract'));
}

}