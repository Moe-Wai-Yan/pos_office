<?php

namespace App\Enums;

class SaleChannelEnum
{
    const POS = 'POS';
    const Online = 'Online';
    const Phone = 'Phone';
    const Facebook = 'Facebook';
    const Instagram = 'Instagram';
    const Lazada = 'Lazada';
    const Shopee = 'Shopee';

    public static function options(): array
    {
        return [
            self::POS,
            self::Online,
            self::Phone,
            self::Facebook,
            self::Instagram,
            self::Lazada,
            self::Shopee,
        ];
    }
}
