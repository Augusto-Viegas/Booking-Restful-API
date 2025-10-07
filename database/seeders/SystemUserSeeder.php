<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemUser;

class SystemUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            ['key' => 'system', 'name' => 'System', 'description' => 'Generic system actor'],
            ['key' => 'cli', 'name' => 'CLI', 'description' => 'Console / artisan operations'],
            ['key' => 'scheduler', 'name' => 'Scheduler', 'description' => 'Scheduled tasks / cron'],
            ['key' => 'importer', 'name' => 'Data importer', 'description' => 'Batch import processes'],
        ];

        foreach($defaults as $d){
            SystemUser::firstOrCreate(['key' => $d['key']], $d);
        }
    }
}
