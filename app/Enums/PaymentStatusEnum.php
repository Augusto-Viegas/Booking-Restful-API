<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this){
            self::PENDING => 'pending',
            self::APPROVED => 'approved',
            self::FAILED => 'failed',
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