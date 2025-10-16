<?php

namespace App\Enums;

class DeliveryPartnerEnum
{
    const None = 'None';
    const NinjaVan = 'NinjaVan';
    const Bee = 'Bee';
    const Royal = 'Royal';
    const Other = 'Other';

    public static function options(): array
    {
        return [
            self::None,
            self::NinjaVan,
            self::Bee,
            self::Royal,
            self::Other,
        ];
    }
}
