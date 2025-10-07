<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\SystemUser;

class SystemUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_actor_creates_and_returns_system_user()
    {
        $actors[] = ['system', 'cli', 'scheduler', 'importer', 'api'];

        foreach($actors as $actor => $name){
            //Ensure that doesn't exist
            SystemUser::where('key', $name)->delete();

            $system = SystemUser::actor($actor); //Creates or return

            $this->assertInstanceOf(SystemUser::class, $system);
            $this->assertEquals($actor, $system->key);

            //Ensure that is in database
            $this->assertDatabaseHas('system_users', [
                'id' => $system->id,
                'key' => $actor
            ]);
        }
    }
}