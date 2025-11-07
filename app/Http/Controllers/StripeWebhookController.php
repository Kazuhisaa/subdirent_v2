<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StripeWebhookController extends Controller
{
   public function handleWebhook(Request $request)
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET'); // from Stripe CLI or Dashboard
        $payload = $request->getContent();
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;

        // ✅ Verify signature
        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('❌ Invalid payload');
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('❌ Invalid signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // 🧩 Handle events
        Log::info('Stripe Event: ' . $event->type);

        switch ($event->type) {

            case 'invoice.finalized':
                $invoice = $event->data->object;
                Log::info("📄 Invoice finalized: {$invoice->id}");
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                Log::info("✅ Invoice paid: {$invoice->id}");

                // --- Database insert/update example ---
                try {
                    $tenantId = $invoice->metadata->tenant_id ?? null;
                    $contractId = $invoice->metadata->contract_id ?? null;

                    if (!$tenantId || !$contractId) {
                        Log::warning("⚠️ Missing metadata for tenant/contract in invoice: {$invoice->id}");
                        break;
                    }

                    DB::table('payments')->insert([
                        'tenant_id' => $tenantId,
                        'contract_id' => $contractId,
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount_paid / 100,
                        'status' => 'paid',
                        'created_at' => now(),
                    ]);

                    Log::info("💾 Payment recorded for tenant {$tenantId}, contract {$contractId}");
                } catch (\Exception $e) {
                    Log::error("❌ Failed to record payment: " . $e->getMessage());
                }

                break;

            case 'charge.succeeded':
                $charge = $event->data->object;
                Log::info("💵 Charge succeeded: {$charge->id}");
                break;

            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                Log::info("✅ Payment intent succeeded: {$intent->id}");
                break;

            default:
                Log::info("Unhandled Stripe event type: " . $event->type);
                break;
        }

        return response('Webhook handled', 200);
    }
}
