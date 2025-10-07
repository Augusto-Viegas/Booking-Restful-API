<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Enums\ServiceSlotStatus;
use App\Models\ServiceSlot;

class ServiceSlotEnumTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_returns_correct_label_for_each_enum_value()
    {
        $slot = new ServiceSlot(['status' => ServiceSlotStatus::AVAILABLE]);
        $this->assertEquals('available', $slot->status_label);
        
        $slot->status = ServiceSlotStatus::BOOKED;
        $this->assertEquals('booked', $slot->status_label);

        $slot->status = ServiceSlotStatus::BLOCKED;
        $this->assertEquals('blocked', $slot->status_label);
    }
}
