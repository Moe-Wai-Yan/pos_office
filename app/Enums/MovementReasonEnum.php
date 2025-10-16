<?php

namespace App\Enums;

class MovementReasonEnum
{
    const Purchase = 'purchase';
    const Sale = 'sale';
    const Return = 'return';
    const Transfer = 'transfer';
    const Adjustment = 'adjustment';
    const Writeoff = 'writeoff';
    const Count = 'count';

    public static function options(): array
    {
        return [
            self::Purchase,
            self::Sale,
            self::Return,
            self::Transfer,
            self::Adjustment,
            self::Writeoff,
            self::Count,
        ];
    }
}
