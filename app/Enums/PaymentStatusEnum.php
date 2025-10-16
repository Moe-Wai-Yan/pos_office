<?php

namespace App\Enums;

class   PaymentStatusEnum
{
    const Paid = 'Paid';
    const Unpaid = 'Unpaid';
    const Partial = 'Partial';

    public static function options(): array
    {
        return [
            self::Paid,
            self::Unpaid,
            self::Partial,
        ];
    }
}
