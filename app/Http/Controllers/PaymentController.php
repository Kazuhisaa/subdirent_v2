<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * ✅ CREATE PAYMENT — Gawa ng PayMongo Checkout Session
     */
    public function createPayment(Request $request, $tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $amount = $request->amount * 100; // PayMongo uses centavos
            $forMonth = $request->for_month; // e.g. 2025-11-16

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(env('PAYMONGO_SECRET_KEY') . ':'),
                'Content-Type' => 'application/json',
            ])->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [[
                            'amount' => $amount,
                            'currency' => 'PHP',
                            'name' => 'Rent Payment',
                            'quantity' => 1,
                        ]],
                        'description' => 'Tenant Monthly Payment',
                        'success_url' => route('tenant.payment.success', $tenant->id),
                        'cancel_url' => route('tenant.payment.cancel', $tenant->id),
                        'metadata' => [
                            'tenant_id' => $tenant->id,
                            'for_month' => $forMonth,
                        ],
                        'payment_method_types' => ['gcash', 'card'],
                    ],
                ],
            ]);

            $checkout = $response->json();

            if (isset($checkout['data']['attributes']['checkout_url'])) {
                return redirect()->away($checkout['data']['attributes']['checkout_url']);
            }

            Log::error('PayMongo Error:', $checkout);
            return back()->with('error', 'Failed to create PayMongo checkout session.');

        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    /**
     * ✅ HANDLE WEBHOOK — Automatic record kapag successful payment
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload, true);

        Log::info('PayMongo Webhook received:', ['payload' => $payload]);

        $type = $event['data']['attributes']['type'] ?? null;

        if ($type !== 'payment.paid') {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $attributes = $event['data']['attributes']['data']['attributes'] ?? [];
            $metadata = $attributes['metadata'] ?? [];

            $tenantId = $metadata['tenant_id'] ?? null;
            $forMonthString = $metadata['for_month'] ?? null;

            $amount = ($attributes['amount'] ?? 0) / 100; // Convert to pesos
            $reference = $attributes['reference_number'] ?? uniqid('PAY-');
            $paymentMethod = $attributes['source']['type'] ?? 'gcash';
            $now = now();

            $tenant = $tenantId ? Tenant::find($tenantId) : null;

            if (!$tenant) {
                $email = $attributes['billing']['email'] ?? null;
                $tenant = $email ? Tenant::where('email', $email)->first() : null;
            }

            if (!$tenant) {
                Log::warning('Webhook: Tenant not found.', ['metadata' => $metadata]);
                return response()->json(['status' => 'tenant_not_found']);
            }

            $contract = Contract::where('tenant_id', $tenant->id)
                ->whereIn('status', ['active', 'ongoing'])
                ->first();

            if (!$contract) {
                Log::warning('Webhook: No active contract found.', ['tenant_id' => $tenant->id]);
                return response()->json(['status' => 'no_active_contract']);
            }

            $forMonthDate = $forMonthString
                ? Carbon::parse($forMonthString)->startOfMonth()
                : $now->startOfMonth();

            $remarks = 'Rent Payment for ' . $forMonthDate->format('F Y');

            $existing = Payment::where('tenant_id', $tenant->id)
                ->whereDate('for_month', $forMonthDate)
                ->where('payment_status', 'paid')
                ->first();

            if ($existing) {
                Log::info('Duplicate payment ignored.', [
                    'tenant' => $tenant->id,
                    'for_month' => $forMonthDate->toDateString(),
                ]);
                return response()->json(['status' => 'duplicate']);
            }

            Payment::create([
                'tenant_id' => $tenant->id,
                'contract_id' => $contract->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_status' => 'paid',
                'payment_date' => $now,
                'for_month' => $forMonthDate,
                'reference_no' => $reference,
                'invoice_no' => 'INV-' . $reference,
                'remarks' => $remarks,
            ]);

            $contract->last_billed_at = $now;
            $contract->save();

            Log::info('Payment recorded successfully.', [
                'tenant' => $tenant->id,
                'for_month' => $forMonthDate->toDateString(),
            ]);

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * ✅ SUCCESS & CANCEL PAGES
     */
    public function success($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        return view('tenant.success', compact('tenant'));
    }

    public function cancel($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        return view('tenant.cancel', compact('tenant'));
    }

    /**
     * ✅ DASHBOARD — ginagamit ang 'for_month' at 'payment_status'
     */
public function dashboard(Tenant $tenant)
{
    try {
        $activeContract = Contract::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->first();

        if (!$activeContract) {
            return back()->with('error', 'No active contract found.');
        }

        $now = Carbon::now();
        // ✅ Ginamit ang due date galing sa contract, hindi hardcoded na '16'
        $billingDay = $activeContract->payment_due_date; 
        $monthlyRent = $activeContract->monthly_payment;

        // 🧾 All paid payments
        $payments = Payment::where('tenant_id', $tenant->id)
            ->where('payment_status', 'paid')
            ->orderBy('payment_date', 'desc') // Mas maganda i-sort by payment date
            ->get();

        // 🧮 Total paid this month
        $totalPaidThisMonth = Payment::where('tenant_id', $tenant->id)
            ->where('payment_status', 'paid')
            ->whereYear('for_month', $now->year)
            ->whereMonth('for_month', $now->month)
            ->sum('amount');

        // 🧩 Remaining balance
        $outstanding = max($monthlyRent - $totalPaidThisMonth, 0);
        $penalty = 0;

        // Optional late fee
        if ($now->day > $billingDay && $outstanding > 0) {
            $daysLate = $now->day - $billingDay;
            if ($daysLate > 5) {
                $penalty = $monthlyRent * 0.02;
                $outstanding += $penalty;
            }
        }

        // ✅ ==============================================
        // ✅ INAYOS ANG NEXT BILLING LOGIC
        // ✅ ==============================================
        
        // 1. Hanapin ang PINAKAHULING (latest) bayad, hindi ang una
        $latestRentPayment = Payment::where('tenant_id', $tenant->id)
            ->where('payment_status', 'paid')
            ->whereNotNull('for_month')
            ->where('remarks', 'not like', '%Deposit%') // Huwag isama ang DP
            ->orderBy('for_month', 'desc') // <-- ✅ CRITICAL FIX: 'desc' para makuha ang huli
            ->first();

        $nextBilling = null;
        if ($latestRentPayment) {
            // Kung ang huling bayad ay Oct 16, ang susunod ay Nov 16
            // Kung ang huling bayad ay Nov 16, ang susunod ay Dec 16
            $nextBilling = Carbon::parse($latestRentPayment->for_month)->addMonth();
        } else {
            // Kung wala pang bayad, kunin ang start date
            $nextBilling = Carbon::parse($activeContract->start_date)->day($billingDay);
            // Kung nagsimula ang contract pagkatapos ng due date, sa next month ang unang bayad
            if (Carbon::parse($activeContract->start_date)->day > $billingDay) {
                 $nextBilling->addMonth();
            }
        }
        
        // 2. Gumawa ng '$nextMonth' array para sa Blade
        $nextMonth = [
            'for_month' => $nextBilling->format('Y-m-d'), // Para sa form
            'date' => $nextBilling->format('M d, Y'),      // Para sa display
        ];

        // 3. Kunin ang amount na babayaran (para sa partial payment)
        $amountToPay = 0;
        if ($outstanding > 0) {
            $amountToPay = $outstanding; // Bayaran muna ang kulang sa buwan na 'to
        } else {
            // Bayad na 'yung buwan na 'to, kaya bayaran 'yung susunod
            $amountToPay = $monthlyRent;
        }


        return view('tenant.payments', compact(
            'tenant',
            'activeContract',
            'payments',
            'nextMonth', // <-- ✅ IPASA ANG TAMANG VARIABLE
            'outstanding',
            'penalty',
            'amountToPay' // ✅ Idinagdag para sa form
        ));

    } catch (\Exception $e) {
        Log::error('Dashboard Error: ' . $e->getMessage());
        return back()->with('error', 'Dashboard error: ' . $e->getMessage());
    }
}

}
