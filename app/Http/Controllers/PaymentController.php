<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        try {
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
                        'success_url' => env('APP_URL') . '/tenant/payment-success',
                        'cancel_url' => env('APP_URL') . '/tenant/payment-cancel',
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
}
