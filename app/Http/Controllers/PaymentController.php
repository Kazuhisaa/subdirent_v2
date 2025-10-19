<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Helper to calculate payments, totals, and next month
     */
    private function getTenantPaymentData(Tenant $tenant)
    {
        $payments = Payment::where('tenant_id', $tenant->id)
            ->where('status', 'paid')
            ->orderBy('for_month')
            ->get();

        $paidMonths = $payments->pluck('for_month')->toArray();
        $leaseStart = Carbon::parse($tenant->lease_start)->startOfMonth();
        $leaseEnd   = Carbon::parse($tenant->lease_end)->startOfMonth();

        $allMonths = [];
        $current = $leaseStart->copy();
        while ($current <= $leaseEnd) {
            $allMonths[] = $current->format('Y-m');
            $current->addMonth();
        }

        $unpaidMonths = array_values(array_diff($allMonths, $paidMonths));

        $totalPaid = $payments->sum('amount');
        $totalDue = count($unpaidMonths) * $tenant->monthly_rent;
        $outstanding = max($totalDue, 0);

        $nextMonth = !empty($unpaidMonths) ? [
            'date' => Carbon::createFromFormat('Y-m', $unpaidMonths[0])->format('F Y'),
            'amount' => $tenant->monthly_rent,
            'for_month' => $unpaidMonths[0],
        ] : [
            'date' => null,
            'amount' => 0,
            'status' => 'All months are paid 🎉',
        ];

        return compact('payments', 'totalPaid', 'totalDue', 'outstanding', 'nextMonth');
    }

    /**
     * Tenant Dashboard
     */
    public function dashboard(Tenant $tenant)
    {
        $data = $this->getTenantPaymentData($tenant);
        return view('tenant.dashboard', compact('tenant') + $data);
    }

    /**
     * Tenant Payments Page
     */

public function payments()
{
    // Get the currently logged-in tenant
    $tenant = Auth::user();

    // Load payments
    $payments = $tenant->payments()->orderBy('for_month', 'desc')->get();

    // Calculate totals
    $totalPaid = $payments->where('status', 'paid')->sum('amount');
    $totalDue = $payments->where('status', 'unpaid')->sum('amount');
    $outstanding = $totalDue; // or your calculation logic

    // Next payment (example logic)
    $nextMonth = [
        'date' => now()->addMonth()->format('F d, Y'),
        'amount' => $tenant->monthly_rent,
        'for_month' => now()->addMonth()->format('Y-m-01'),
    ];

    return view('tenant.payments', compact(
        'tenant',
        'payments',
        'totalPaid',
        'totalDue',
        'outstanding',
        'nextMonth'
    ));
}

    /**
     * Create PayMongo Payment
     */
    public function createPayment(Request $request, Tenant $tenant)
    {
        $method = $request->input('payment_method');
        $forMonth = $request->input('for_month', now()->format('Y-m'));

        if (!in_array($method, ['card', 'gcash'])) {
            return redirect()->route('tenant.dashboard', $tenant->id)
                ->with('error', 'Invalid payment method.');
        }

        $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [[
                            'name' => "Monthly Rent - {$tenant->first_name}",
                            'quantity' => 1,
                            'currency' => 'PHP',
                            'amount' => intval($tenant->monthly_rent * 100),
                        ]],
                        'payment_method_types' => [$method],
                        'success_url' => route('payments.success', $tenant->id, false),
                        'cancel_url' => route('payments.cancel', $tenant->id, false),
                        'description' => "Rent payment for {$tenant->first_name}",
                        'metadata' => [
                            'tenant_id' => $tenant->id,
                            'for_month' => $forMonth,
                        ]
                    ]
                ]
            ]);

        $checkout = $response->json();
        Log::info('PayMongo Checkout Response:', $checkout);

        if (isset($checkout['data']['attributes']['checkout_url'])) {
            return redirect()->away($checkout['data']['attributes']['checkout_url']);
        }

        return back()->with(
            'error',
            'Failed to create payment session. Response: ' . json_encode($checkout)
        );
    }

    /**
     * Payment Success Redirect
     */
    public function success(Tenant $tenant)
    {
        return redirect()->route('tenant.dashboard', $tenant->id)
            ->with('success', 'Payment successful!');
    }

    /**
     * Payment Cancel Redirect
     */
    public function cancel(Tenant $tenant)
    {
        return redirect()->route('tenant.dashboard', $tenant->id)
            ->with('error', 'Payment cancelled.');
    }

    /**
     * PayMongo Webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayMongo Webhook: ' . json_encode($payload));

        $eventType = $payload['data']['attributes']['type'] ?? null;
        $checkoutData = $payload['data']['attributes']['data']['attributes'] ?? [];

        $tenantId = $checkoutData['metadata']['tenant_id'] ?? null;
        $forMonth = $checkoutData['metadata']['for_month'] ?? now()->format('Y-m');

        $payments = $checkoutData['payments'] ?? [];
        $paymentStatus = $payments[0]['attributes']['status'] ?? null;
        $referenceId   = $payments[0]['id'] ?? null;
        $paidAt        = $payments[0]['attributes']['paid_at'] ?? now();

        if (!$tenantId) return response()->json(['status' => 'error', 'message' => 'No tenant ID'], 400);

        $tenant = Tenant::find($tenantId);
        if (!$tenant) return response()->json(['status' => 'error', 'message' => 'Tenant not found'], 404);

        if ($eventType === 'checkout_session.payment.paid' && $paymentStatus === 'paid') {
            $exists = Payment::where('tenant_id', $tenantId)
                ->where('for_month', $forMonth)
                ->where('method', 'PayMongo')
                ->where('status', 'paid')
                ->exists();

            if (!$exists) {
                Payment::create([
                    'tenant_id' => $tenant->id,
                    'amount' => $tenant->monthly_rent,
                    'payment_date' => now(),
                    'paid_at' => Carbon::createFromTimestamp($paidAt),
                    'status' => 'paid',
                    'method' => 'PayMongo',
                    'for_month' => $forMonth,
                    'reference_id' => $referenceId,
                ]);
                Log::info("Payment saved: Tenant {$tenant->id}, Month {$forMonth}");
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
