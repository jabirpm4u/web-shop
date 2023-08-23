<?php

namespace App\Contracts;

interface PaymentServiceContract
{
    public function makePayment($orderId, $customerEmail, $value);
}
