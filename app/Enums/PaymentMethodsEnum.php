<?php

namespace App\Enums;

enum PaymentMethodsEnum: string
{
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';
    case CASH = 'cash';

    public function label(): string
    {
        return match($this){
            self::CREDIT_CARD => 'credit_card',
            self::DEBIT_CARD => 'debit_card',
            self::PAYPAL => 'paypal',
            self::BANK_TRANSFER => 'bank_transfer',
            self::CASH => 'cash',
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