<?php

namespace App\Enums;

class DiscountTypeEnum
{
    const Percent = 'percent';
    const Amount = 'amount';

    public static function options(): array
    {
        return [
            self::Percent,
            self::Amount,
        ];
    }
}
