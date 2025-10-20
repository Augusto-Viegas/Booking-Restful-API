<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case EMPLOYEE = 'employee';

    public function label(): string
    {
        return match($this){
            self::EMPLOYEE => 'employee',
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