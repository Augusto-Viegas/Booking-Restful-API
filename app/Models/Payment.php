<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentMethodsEnum;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethodsEnum::class,
            'status' => PaymentStatusEnum::class,
        ];
    }

    public function getPaymentMethodsLabelAttribute(): string
    {
        return $this->payment_method->label();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Models relationships
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
