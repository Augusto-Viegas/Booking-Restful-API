<?php

namespace App\Services\Payment;

use App\Enums\PaymentStatusEnum;
use App\Interfaces\Payment\PaymentGatewayInterface;

class FakePaymentGateway implements PaymentGatewayInterface
{
    public function process(float $amount, string $method): array
    {
        #Simulate a successful payment response
        usleep(500000); // Simulate network delay
        return [
            'status' => PaymentStatusEnum::APPROVED,
            'transaction_id' => uniqid('txn_'),
            'message' => 'Simulated payment processed successfully.',
        ];
    }
}