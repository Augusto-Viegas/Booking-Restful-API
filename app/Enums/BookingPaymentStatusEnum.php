<?php

namespace App\Enums;

enum BookingPaymentStatusEnum: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this){
            self::PAID => 'paid',
            self::UNPAID => 'unpaid',
            self::REFUNDED => 'refunded',
        };
    }

    public static function values(): array
    {
        return array_map(fn($c) => $c->value, self::cases());
    }

    public static function toSelect(): array
    {
        return array_combine(
            self::values(),
            array_map(fn($c) => $c->label(), self::cases())
        );
    } 
}
