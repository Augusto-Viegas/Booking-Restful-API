<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceSlot;
use App\Models\Booking;
use App\Enums\ServiceSlotStatus;
use App\Enums\BookingStatusEnum;
use App\Enums\BookingPaymentStatusEnum;

class BookingFeatureEnumTest extends TestCase
{
    use RefreshDatabase;

    public function test_persists_enums_and_return_label()
    {
        $customer = Customer::factory()->create();

        $service = Service::factory()->create();

        $serviceSlot = ServiceSlot::create([
            'service_id' => $service->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '17:00',
            'capacity' => 2,
            'status' => ServiceSlotStatus::AVAILABLE,
        ]);
        
        $booking = Booking::create([
            'service_id' => $service->id,
            'service_slot_id' => $serviceSlot->id,
            'customer_id' => $customer->id,
            'status' => BookingStatusEnum::PENDING,
            'payment_status' => BookingPaymentStatusEnum::UNPAID,
        ]);

        $this->assertDatabaseHas('service_slots', [
            'id' => $serviceSlot->id,
            'status' => ServiceSlotStatus::AVAILABLE,
        ]);

        $this->assertEquals('available', $serviceSlot->status_label);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatusEnum::PENDING,
            'payment_status' => BookingPaymentStatusEnum::UNPAID,
        ]);

        $this->assertEquals('pending', $booking->status_label);
        $this->assertEquals('unpaid', $booking->payment_status_label);
        
    }
}
