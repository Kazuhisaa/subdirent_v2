<?php

namespace App\Http\Controllers;

use App\Models\Autopay;
use App\Models\Tenant;
use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;

class AutopayController extends Controller
{
    /**
     * SETUP AUTOPAY — Stripe Subscription
     */
 public function setupAutopay(Request $request, $tenantId, $contractId)
{
    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        // 1️⃣ Find Tenant and Contract
        $tenant = Tenant::findOrFail($tenantId);
        $contract = Contract::findOrFail($contractId);
        $amount = (int)($contract->monthly_payment * 100); // Stripe uses cents

        Log::info("💡 Setting up autopay for Tenant {$tenant->id}, Contract {$contract->id} | ₱{$contract->monthly_payment}");
        Log::info('🧩 DEBUG IDs', [
  'tenant_id' => $tenant->id,
  'contract_id' => $contract->id,
  'payment_method' => $request->payment_method
]);


        // 2️⃣ Create Stripe Customer with metadata
        $customer = \Stripe\Customer::create([
            'email' => $tenant->email ?? 'noemail@tenant.com',
            'name' => $tenant->name ?? 'Unnamed Tenant',
            'payment_method' => $request->payment_method,
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
            'metadata' => [
                'tenant_id' => (string)$tenant->id,
                'contract_id' => (string)$contract->id,
            ],
        ]);

        Log::info("👤 Customer created: {$customer->id}");

        // 3️⃣ Create recurring price
        $price = \Stripe\Price::create([
            'currency' => 'php',
            'unit_amount' => $amount,
            'recurring' => ['interval' => 'month'],
            'product_data' => [
                'name' => 'Rent Contract #' . $contract->id,
            ],
        ]);

        Log::info("💰 Price created: {$price->id}");

        // 4️⃣ Create Subscription with metadata
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [[ 'price' => $price->id ]],
            'default_payment_method' => $request->payment_method,
            'expand' => ['latest_invoice.payment_intent'],
            'payment_settings' => [
                'save_default_payment_method' => 'on_subscription',
            ],
            'metadata' => [
                'tenant_id' => (string)$tenant->id,
                'contract_id' => (string)$contract->id,
            ],
        ]);

        Log::info("📅 Subscription created: {$subscription->id}");

        // 5️⃣ Ensure metadata is applied to both subscription & invoice
        \Stripe\Subscription::update($subscription->id, [
            'metadata' => [
                'tenant_id' => (string)$tenant->id,
                'contract_id' => (string)$contract->id,
            ],
        ]);

        if (isset($subscription->latest_invoice) && is_object($subscription->latest_invoice)) {
    $invoiceId = $subscription->latest_invoice->id;
} else {
    $invoiceId = $subscription->latest_invoice;
}

if ($invoiceId) {
    \Stripe\Invoice::update($invoiceId, [
        'metadata' => [
            'tenant_id' => (string)$tenant->id,
            'contract_id' => (string)$contract->id,
        ],
    ]);

    Log::info("🧾 Metadata added to invoice: {$invoiceId}");
}

        // 6️⃣ Save to Autopay table
        $autopay = Autopay::create([
            'tenant_id' => $tenant->id,
            'contract_id' => $contract->id,
            'stripe_customer_id' => $customer->id,
            'stripe_subscription_id' => $subscription->id,
            'stripe_payment_method_id' => $request->payment_method,
            'status' => 'active',
            'last_billed_at' => now(),
            'next_due_date' => now()->addMonth(),
        ]);

        Log::info("✅ Autopay activated for Tenant {$tenant->id}, Contract {$contract->id}");

       return redirect()
    ->route('tenant.payments', ['tenantId' => $tenant->id])
    ->with('success', 'Autopay successfully activated for this tenant.');


    } catch (\Exception $e) {
        Log::error('❌ Stripe Autopay setup error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Autopay setup failed: ' . $e->getMessage(),
        ], 500);
    }
}




    /**
     * CANCEL AUTOPAY
     */
    public function cancel($autopayId)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $autopay = Autopay::findOrFail($autopayId);

            if ($autopay->stripe_subscription_id) {
                $subscription = Subscription::retrieve($autopay->stripe_subscription_id);
                $subscription->cancel();
            }

            $autopay->update(['status' => 'canceled']);
            return back()->with('success', 'Autopay canceled successfully.');
        } catch (\Exception $e) {
            Log::error('Autopay cancel error: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel autopay.');
        }
    }

    /**
     * DOWNLOAD INVOICE PDF
     */
    public function downloadInvoice($autopayId)
    {
        $autopay = Autopay::findOrFail($autopayId);
        $disk = 'public';

        if (!$autopay->remarks || !Storage::disk($disk)->exists($autopay->remarks)) {
            abort(404, "Invoice not found for Autopay ID: {$autopay->id}");
        }

        return Storage::disk($disk)->download($autopay->remarks, 'Invoice-' . basename($autopay->remarks));
    }

    /**
     * ADMIN — View All Autopay Records
     */
    public function index()
    {
        $autopays = Autopay::with(['tenant', 'contract'])->get();
        $archivedAutopays = Autopay::onlyTrashed()->with(['tenant', 'contract'])->get();

        return view('admin.autopay', compact('autopays', 'archivedAutopays'));
    }

    public function archive($id)
    {
        $autopay = Autopay::findOrFail($id);
        $autopay->delete();

        return response()->json(['message' => 'Autopay archived successfully.']);
    }

    public function restore($id)
    {
        $autopay = Autopay::withTrashed()->findOrFail($id);
        $autopay->restore();

        return response()->json(['message' => 'Autopay restored successfully.']);
    }

    // AutopayController.php
public function pause(Autopay $autopay)
{
    $autopay->update(['status' => 'paused']);
    return back()->with('success', 'Autopay paused.');
}

public function activate(Autopay $autopay)
{
    $autopay->update(['status' => 'active']);
    return back()->with('success', 'Autopay activated.');
}
}