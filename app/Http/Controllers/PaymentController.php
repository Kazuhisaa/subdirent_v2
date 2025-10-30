<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
  
class PaymentController extends Controller
{
    /**
     * CREATE PAYMENT — PayMongo Checkout
     */
    public function createPayment(Request $request, $tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            $amount = $request->amount * 100; // PayMongo uses centavos
            $forMonth = $request->for_month; // e.g. 2025-12-16

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
        $amount = ($attributes['amount'] ?? 0) / 100;
        $reference = $attributes['reference_number'] ?? uniqid('PAY-');

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

        $now = now();

        $isDownpayment = stripos($attributes['description'] ?? '', 'Deposit') !== false;

        // Determine starting month
        $downpaymentMonth = Payment::where('tenant_id', $tenant->id)
            ->where('contract_id', $contract->id)
            ->where('remarks', 'Downpayment')
            ->orderBy('for_month', 'asc')
            ->value('for_month');

        $currentMonth = $downpaymentMonth ? Carbon::parse($downpaymentMonth) : Carbon::parse($contract->start_date);

        // Determine next month for Rent Payment
        if ($isDownpayment) {
            $forMonthDate = $currentMonth->copy();
            $remarks = 'Downpayment';
        } else {
            // Group payments by month and find first month not fully paid
            $monthlyPayments = Payment::where('tenant_id', $tenant->id)
                ->where('contract_id', $contract->id)
                ->where('remarks', 'like', 'Rent Payment%')
                ->get()
                ->groupBy(function($payment) {
                    return Carbon::parse($payment->for_month)->format('Y-m');
                });

            $nextMonth = $currentMonth->copy()->addMonth()->day($contract->payment_due_date);

            foreach ($monthlyPayments as $month => $payments) {
                $totalPaid = $payments->sum('amount');
                if ($totalPaid < $contract->monthly_payment) {
                    $nextMonth = Carbon::parse($month)->day($contract->payment_due_date);
                    break;
                } else {
                    $nextMonth = Carbon::parse($month)->addMonth()->day($contract->payment_due_date);
                }
            }

            $forMonthDate = $nextMonth;
            $remarks = 'Rent Payment for ' . $forMonthDate->format('F Y');
        }

        // Determine payment status
        $monthlyPayment = $contract->monthly_payment;

        $existingPaid = Payment::where('tenant_id', $tenant->id)
            ->whereYear('for_month', $forMonthDate->year)
            ->whereMonth('for_month', $forMonthDate->month)
            ->whereIn('payment_status', ['paid','partial'])
            ->sum('amount');

        $totalPaid = $existingPaid + $amount;
        $paymentStatus = $totalPaid >= $monthlyPayment ? 'paid' : 'partial';

        // Create payment record
        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'contract_id' => $contract->id,
            'amount' => $amount,
            'payment_method' => 'gcash',
            'payment_status' => $paymentStatus,
            'payment_date' => $now,
            'for_month' => $forMonthDate,
            'reference_no' => $reference,
            'invoice_no' => 'INV-' . $reference,
            'remarks' => $remarks,
        ]);

       
$invoiceFilename = 'invoices/' . $payment->invoice_no . '.pdf';

// Siguraduhin na yung 'invoices' folder ay nage-exist
$invoiceDirectory = storage_path('app/public/invoices');
if (!file_exists($invoiceDirectory)) {
    // Gumawa ng folder kung wala pa
    mkdir($invoiceDirectory, 0775, true);
}
    
$pdf = PDF::loadView('tenant.invoice', [
    'payment' => $payment,
    'tenant' => $tenant,
    'contract' => $contract,
])
->setOptions(['isRemoteEnabled' => true]);

$invoiceFilename = 'invoices/INV-PAY-' . $payment->reference_no . '.pdf';
$pdf->save(storage_path('app/public/' . $invoiceFilename));

$payment->invoice_pdf = $invoiceFilename;

// Optional: force save check
if(!$payment->save()) {
    Log::error("Failed to save invoice PDF path for payment ID: {$payment->id}");
}

        if ($paymentStatus === 'paid') {
            $contract->last_billed_at = $now;
            $contract->save();
        }

        Log::info('Payment recorded successfully with invoice.', [
            'tenant' => $tenant->id,
            'for_month' => $forMonthDate->toDateString(),
            'status' => $paymentStatus,
            'invoice' => $invoiceFilename
        ]);

        return response()->json(['status' => 'ok', 'payment_status' => $paymentStatus]);

    } catch (\Exception $e) {
        Log::error('Webhook error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

    /**
     * DASHBOARD — Next month & outstanding
     */
  public function dashboard(Tenant $tenant)
    {
        try {
            $activeContract = Contract::where('tenant_id', $tenant->id)
                ->whereIn('status', ['active', 'ongoing'])
                ->first();

            if (!$activeContract) {
                return back()->with('error', 'No active contract found.');
            }

            $now = Carbon::now();
            $billingDay = $activeContract->payment_due_date;
            $monthlyRent = $activeContract->monthly_payment;

            // Kunin lahat ng payments para sa history
            $payments = Payment::where('tenant_id', $tenant->id)
                ->whereIn('payment_status', ['paid', 'partial'])
                ->orderBy('for_month', 'asc')
                ->get();

            // Kunin lahat ng "Rent Payment" para sa computation
            $rentPayments = $payments->where('remarks', 'like', 'Rent Payment%');

            // I-group payments per month
            $paymentsByMonth = $rentPayments->groupBy(function($payment) {
                return Carbon::parse($payment->for_month)->format('Y-m');
            });

            // Simula month = next month after downpayment
            $downpayment = $payments->where('remarks', 'Downpayment')->first();
            $currentMonth = $downpayment
                ? Carbon::parse($downpayment->for_month)->copy()->addMonth()
                : Carbon::parse($activeContract->start_date);
            $currentMonth->day($billingDay);

            $outstanding = $monthlyRent;

            // Hanapin month na may kulang pa
            foreach ($paymentsByMonth as $month => $monthPayments) {
                $totalPaid = $monthPayments->sum('amount');

                if ($totalPaid < $monthlyRent) {
                    $outstanding = $monthlyRent - $totalPaid;
                    $currentMonth = Carbon::parse($month . '-' . $billingDay);
                    break;
                } else {
                    // Full payment, move to next month
                    $currentMonth->addMonth();
                    $outstanding = $monthlyRent;
                }
            }

            // Penalty kung overdue sa current month
            $penalty = 0;
            if ($now->greaterThan($currentMonth) && $outstanding > 0) {
                $daysLate = $now->diffInDays($currentMonth);
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

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Dashboard error: ' . $e->getMessage());
        }
    }

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

public function downloadInvoice(Payment $payment)
{
    $disk = 'public'; // dahil dine-save natin sa storage/app/public

    if (!$payment->invoice_pdf || !Storage::disk($disk)->exists($payment->invoice_pdf)) {
        abort(404, "Invoice not found for Payment #{$payment->id}");
    }

    // download method
    return torage::disk($disk)->download($payment->invoice_pdf, 'Invoice-' . $payment->invoice_no . '.pdf');
}


public function index()
{
    $payments = Payment::with(['tenant', 'contract'])->get();
    $archivedPayments = Payment::onlyTrashed()->with(['tenant', 'contract'])->get();

    return view('admin.payments', compact('payments', 'archivedPayments'));
}



public function archive($id)
{
    $payment = Payment::findOrFail($id);
    $payment->delete(); // assuming you're using SoftDeletes
    return redirect()->route('admin.payments')->with('success', 'Payment archived successfully.');
}

public function viewArchive()
{
    $archived = Payment::onlyTrashed()->with('tenant')->get();
    return response()->json($archived);
}

public function restore($id)
{
    $payment = Payment::withTrashed()->findOrFail($id);
    $payment->restore();

    if (request()->ajax()) {
        return response()->json(['message' => 'Payment restored successfully.']);
    }

    return redirect()->route('admin.payments')->with('success', 'Payment restored successfully.');
}


}
