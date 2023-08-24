<?php

namespace App\Services;

use App\Contracts\PaymentServiceContract;
use Illuminate\Support\Facades\Http;

class SuperPaymentService implements PaymentServiceContract
{
    public function makePayment($orderId, $customerEmail, $value)
    {
        $paymentUrl = 'https://superpay.view.agentur-loop.com/pay';

        // Simulate payment request payload
        $paymentRequestData = [
            'order_id' => $orderId,
            'customer_email' => $customerEmail, // Change this to the customer's email
            'value' => $value,
        ];

        try{

            // Send payment request to the Super Payment Provider
            $response = Http::post($paymentUrl, $paymentRequestData);
            if ($response->successful()) {
                $responseData = $response->json();
                return($responseData['message'] === 'Payment Successful')? true:false;
            } else {
                return false; // Payment failed
            }
        } catch (\Exception $exception) {
            return false;
        }
    }
}
