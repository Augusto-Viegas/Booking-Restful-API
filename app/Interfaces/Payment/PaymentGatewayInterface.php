<?php

namespace App\Interfaces\Payment;

interface PaymentGatewayInterface
{
    public function process(float $amount, string $method): array;
}