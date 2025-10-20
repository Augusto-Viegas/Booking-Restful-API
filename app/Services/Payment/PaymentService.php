<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Booking;
use App\Interfaces\Payment\PaymentGatewayInterface;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;

    public function __construct(PaymentGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function createPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data){
            $booking = Booking::findOrFail($data['booking_id']);

            $response = $this->gateway->process($data['amount'], $data['payment_method']);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'status' => $response['status'],
            ]);

            if($response['status'] === PaymentStatusEnum::APPROVED->value){
                $booking->update([
                    'payment_status' => PaymentStatusEnum::APPROVED,
                    'status' => 'confirmed',
                ]);
            }

            return $payment;
        });
    }
}