<?php

namespace App\Enums;

enum FinanceSource: string
{
    case BCA = 'BCA';
    case GOPAY = 'Gopay';
    case OVO = 'OVO';
    case OTHER = 'Other';

    

    /**
     * Get all the values of the enum
     *
     * @return array
     */
    public static function values(): array
    {
        return [
            self::BCA->value,
            self::GOPAY->value,
            self::OVO->value,
            self::OTHER->value,
        ];
    }

    /**
     * Get all the keys of the enum
     *
     * @return array
     */
    public static function keys(): array
    {
        return [
            'BCA',
            'GOPAY',
            'OVO',
            'OTHER'
        ];
    }

    /**
     * Get the key of the enum based on the value
     *
     * @param string $value
     * @return string
     */
    public static function key(string $value): FinanceSource
    {
        return match ($value) {
            'BCA' => self::BCA,
            'GOPAY' => self::GOPAY,
            'OVO' => self::OVO,
            'OTHER' => self::OTHER,
            default => self::OTHER,
        };
    }

}