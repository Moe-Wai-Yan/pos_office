<?php

namespace App\Enums;

class TransferStatusEnum
{
    const Requested = 'Requested';
    const Shipped = 'Shipped';
    const Received = 'Received';
    const Cancelled = 'Cancelled';

    public static function options(): array
    {
        return [
            self::Requested,
            self::Shipped,
            self::Received,
            self::Cancelled,
        ];
    }
}
