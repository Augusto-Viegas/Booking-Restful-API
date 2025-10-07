<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceSlot;
use App\Models\ActivityLog;
use Tests\TestCase;

class ServiceSlotLoggingUserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_employee_user_type(): void
    {
        //Creates necessary data
        $user = User::factory()->create();
        $service = Service::factory()->create();

        //Log user
        $this->actingAs($user);

        //Create slot of a service (LogsActivity must trigger at this step)
        $slot = ServiceSlot::create([
            'service_id' => $service->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => '07:00',
            'end_time' => '17:00',
            'capacity' => 1,
            'status' => 'available',
        ]);

        //Assert: activity_logs must have a register with an user_id equals to user
        $this->assertDatabaseHas('activity_logs',[
            'user_id' => $user->id,
            'origin' => 'user',
            'action' => 'created',
            'entity_type' => 'ServiceSlot',
            'entity_id' => $slot->id,
        ]);

        /**
         * Optional:
         * - Verify registered content
        **/
        $log = ActivityLog::where('entity_type', 'ServiceSlot')
            ->where('entity_id', $slot->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('user', $log->origin);
        $this->assertEquals($user->id, $log->user_id);
    }
}
