<?php

namespace App\Enums;

class PurchaseStatusEnum
{
    const Draft = 'Draft';
    const Ordered = 'Ordered';
    const Partially_Received = 'Partially_Received';
    const Received = 'Received';
    const Cancelled = 'Cancelled';

    public static function options(): array
    {
        return [
            self::Draft,
            self::Ordered,
            self::Partially_Received,
            self::Received,
            self::Cancelled,
        ];
    }
}
