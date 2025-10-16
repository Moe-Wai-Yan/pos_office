<?php

namespace App\Enums;

class PaymentMethodEnum
{
    const Cash = 'Cash';
    const Card = 'Card';
    const Mobile = 'Mobile';
    const Bank = 'Bank';
    const GiftCard = 'GiftCard';
    const StoreCredit = 'StoreCredit';
    const Mixed = 'Mixed';

    public static function options(): array
    {
        return [
            self::Cash,
            self::Card,
            self::Mobile,
            self::Bank,
            self::GiftCard,
            self::StoreCredit,
            self::Mixed,
        ];
    }
}
