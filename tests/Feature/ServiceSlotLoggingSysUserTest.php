<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Service;
use App\Models\SystemUser;
use App\Models\ServiceSlot;
use App\Models\ActivityLog;

class ServiceSlotLoggingSysUserTest extends TestCase
{
   public function test_system_user_type()
    {
        //Ensure that system user CLI exists
        $system = SystemUser::firstOrCreate(['key' => 'cli'],[
            'name' => 'CLI','description' => 'Laravel artisan command',
        ]);

        $this->app->instance('system_user_context', $system);

        $service = Service::factory()->create();

        $slot = ServiceSlot::create([
            'service_id' => $service->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => '07:00',
            'end_time' => '17:00',
            'capacity' => 1,
            'status' => 'available'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'system_user_id' => $system->id,
            'origin' => 'system',
            'action' => 'created',
            'entity_type' => 'ServiceSlot',
            'entity_id' => $slot->id,
        ]);

        $log = ActivityLog::where('entity_type', 'ServiceSlot')
            ->where('entity_id', $slot->id)
            ->first();
        $this->assertNotNull($log);
        $this->assertEquals('system', $log->origin);
        $this->assertEquals($system->id, $log->system_user_id);
    }
}
