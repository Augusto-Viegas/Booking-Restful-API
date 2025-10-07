<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
    ];


    /**
     * Relationships
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_service');
    }

    public function serviceSlots()
    {
        return $this->hasMany(ServiceSlot::class);
    }
}
