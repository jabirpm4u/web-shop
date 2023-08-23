<?php

namespace App\Services;

use App\Contracts\PaymentServiceContract;

class SuperPaymentService implements PaymentServiceContract
{
    public function makePayment($orderId, $customerEmail, $value)
    {
        // Implementation for Super Payment Provider
    }
}
