<?php

namespace App\Enums;

class SaleStatusEnum
{
    const Pending = 'Pending';
    const Completed = 'Completed';
    const Cancelled = 'Cancelled';
    const Refunded = 'Refunded';

    public static function options(): array
    {
        return [
            self::Pending,
            self::Completed,
            self::Cancelled,
            self::Refunded,
        ];
    }
}
