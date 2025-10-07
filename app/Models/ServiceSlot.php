<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use App\Enums\ServiceSlotStatus;

class ServiceSlot extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'service_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'capacity' => 'integer',
            'status' => ServiceSlotStatus::class,
        ];
    }

    /**
     * Check if the service slot has available capacity
     * Refatorar essa merda aqui.
     */
    public function hasAvailableCapacity()
    {
        return $this->booked < $this->capacity;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Models relationships
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function bookings()
    {
        return $this->hasOne(Booking::class);
    }

}
