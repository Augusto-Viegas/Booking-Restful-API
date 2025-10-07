<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Booking;
use App\Enums\BookingStatusEnum;
use App\Enums\BookingPaymentStatusEnum;

class BookingEnumTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_returns_correct_label_for_each_enum_value()
    {
        $booking = new Booking(['status' => BookingStatusEnum::CONFIRMED]);
        $this->assertEquals('confirmed', $booking->status_label);

        $booking->status = BookingStatusEnum::PENDING;
        $this->assertEquals('pending', $booking->status_label);

        $booking->status = BookingStatusEnum::CANCELED;
        $this->assertEquals('canceled', $booking->status_label);

    }

    public function test_returns_correct_payment_status_label_for_each_enum_value()
    {
        $booking = new Booking(['payment_status' => BookingPaymentStatusEnum::PAID]);
        $this->assertEquals('paid', $booking->payment_status_label);

        $booking->payment_status = BookingPaymentStatusEnum::UNPAID;
        $this->assertEquals('unpaid', $booking->payment_status_label);

        $booking->payment_status = BookingPaymentStatusEnum::REFUNDED;
        $this->assertEquals('refunded', $booking->payment_status_label);
    }
}
