<?php

namespace App\Enums;

enum ServiceSlotStatus: string
{
    case AVAILABLE = 'available';
    case BOOKED = 'booked';
    case BLOCKED = 'blocked';

    //Labels to use on UI.
    public function label(): string
    {
        return match($this){
            self::AVAILABLE => 'available',
            self::BOOKED => 'booked',
            self::BLOCKED => 'blocked',
        };
    }

    //Retorna array de valores
    public static function values(): array
    {
        return array_map(fn($c) => $c->value, self::cases());
    }

    //retorna opções para <select> ['available' => 'available']
    public static function toSelect(): array
    {
        return array_combine(
            self::values(),
            array_map(fn($c) => $c->label(), self::cases())
        );
    }

}
