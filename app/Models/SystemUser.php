<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUser extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'is_active'
    ];

    public static function actor($systemActor, ?string $description = null): ?self
    {
        return self::firstOrCreate(
            ['key' => strtolower($systemActor)],
            ['name' => strtoupper($systemActor)],
            ['description' => $description ?? "Generic system actor"],
        );
    }

    public function acitivityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
