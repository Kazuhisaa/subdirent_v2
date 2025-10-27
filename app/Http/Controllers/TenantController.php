<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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
            'nextDueDate' => $nextDueDate,
        ]);
    }

    public function property()
    {
        $user = Auth::user()->load('tenant.unit', 'tenant.contracts');
        if (! $user->tenant) {
            abort(404, 'Tenant record not found.');
        }
        $activeContract = $user->tenant->contracts->whereIn('status', ['active', 'ongoing'])->first();

        return view('tenant.property', [
            'tenant' => $user,
            'contract' => $activeContract,
            'unit' => $user->tenant->unit,
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

    return view('tenant.payments', compact(
        'tenant',
        'activeContract',
        'payments',
        'nextMonth',
        'outstanding',
        'penalty',
        'amountToPay'
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
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
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
        $archived = Tenant::onlyTrashed()->get();

        return response()->json($archived);
    }
}
