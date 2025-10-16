<?php

namespace App\Enums;

class LocationTypeEnum
{
    const Store = 'Store';
    const Warehouse = 'Warehouse';

    public static function options(): array
    {
        return [
            self::Store,
            self::Warehouse,
        ];
    }
}
