<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BookingStatusEnum;
use App\Enums\BookingPaymentStatusEnum;

class Booking extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'service_id',
        'service_slot_id',
        'customer_id',
        'status',
        'payment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatusEnum::class,
            'payment_status' => BookingPaymentStatusEnum::class,
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return $this->payment_status->label();
    }

    /**
     * Models relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'booking_service');
    }

    public function serviceSlot()
    {
        return $this->belongsTo(ServiceSlot::class, 'service_slot_id');
    }
}
