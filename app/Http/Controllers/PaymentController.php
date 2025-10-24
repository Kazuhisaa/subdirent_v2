<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function createPayment(Request $request , $tenantId)
    {
        try {
          $tenant = Tenant::findOrFail($tenantId); 
            // Example values (palitan mo ng real data mo)
            $amount = $request->amount * 100; // PayMongo uses centavos
            $email = $request->email ?? 'test@example.com';

            // Create checkout session sa PayMongo
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'authorization' => 'Basic ' . base64_encode(env('PAYMONGO_SECRET_KEY') . ':'),
            ])->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'description' => 'Tenant Monthly Payment',
                        'line_items' => [[
                            'currency' => 'PHP',
                            'amount' => $amount,
                            'name' => 'Rent Payment',
                            'quantity' => 1,
                        ]],
                        'payment_method_types' => ['gcash', 'card'],
                        'success_url' => env('APP_URL') . "/tenant/{$tenant->id}/payment-success",
                        'cancel_url' => env('APP_URL') . "/tenant/{$tenant->id}/payment-cancel",
                    ],
                ],
            ]); 

            $checkout = $response->json();

            if (isset($checkout['data']['attributes']['checkout_url'])) {
                return redirect()->away($checkout['data']['attributes']['checkout_url']);
            } else {
                return back()->with('error', 'Failed to create PayMongo checkout session.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

public function handleWebhook(Request $request)
{
    // Basahin ang payload mula sa PayMongo
    $payload = $request->getContent();
    $signature = $request->header('Paymongo-Signature');

    // Optional: i-log mo muna para makita mo kung anong dumadating
    Log::info('PayMongo Webhook received:', [
        'signature' => $signature,
        'payload' => $payload,
    ]);

    // Decode JSON payload
    $event = json_decode($payload, true);

    if (isset($event['data']['attributes']['type'])) {
        $type = $event['data']['attributes']['type'];

        if ($type === 'payment.paid') {
            // Handle successful payment
            Log::info('Payment successful event received!');
        } elseif ($type === 'payment.failed') {
            Log::warning('Payment failed event received.');
        }
    }

    // Always return 200 OK para hindi mag-retry si PayMongo
    return response()->json(['status' => 'ok']);
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
public function dashboard($tenantId)
{
    $tenant = Tenant::findOrFail($tenantId);
    return view('tenant.dashboard', compact('tenant'));
}

}
