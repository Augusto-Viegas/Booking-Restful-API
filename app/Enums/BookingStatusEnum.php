<?php

namespace App\Enums;

enum BookingStatusEnum: string
{
    case CONFIRMED = 'confirmed';
    case PENDING = 'pending';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match($this){
            self::CONFIRMED => 'confirmed',
            self::PENDING => 'pending',
            self::CANCELED => 'canceled',
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
