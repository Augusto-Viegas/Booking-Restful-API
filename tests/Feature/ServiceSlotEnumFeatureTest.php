<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Service;
use App\Models\ServiceSlot;
use App\Enums\ServiceSlotStatus;

class ServiceSlotEnumFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_persists_enum_status_and_returns_label()
    {
        $service = Service::factory()->create();

        $slot = ServiceSlot::create([
            'service_id' => $service->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '13:00',
            'capacity' => 2,
            'status' => ServiceSlotStatus::BOOKED,
        ]);

        $this->assertDatabaseHas('service_slots', [
            'id' => $slot->id,
            'status' => ServiceSlotStatus::BOOKED,
        ]);

        $this->assertEquals('booked', $slot->status_label);
    }
}
