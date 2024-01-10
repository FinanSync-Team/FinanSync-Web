<?php

namespace App\Enums;

enum FinanceCategory: string
{
    case TRANSPORT = 'Transport';
    case BILLS = 'Bills';
    case FOOD = 'Food';
    case OTHER = 'Other';
    

    /**
     * Get all the values of the enum
     *
     * @return array
     */
    public static function values(): array
    {
        return [
            self::TRANSPORT->value,
            self::BILLS->value,
            self::FOOD->value,
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
            'TRANSPORT',
            'BILLS',
            'FOOD',
            'OTHER'
        ];
    }

    /**
     * Get the key of the enum based on the value
     *
     * @param string $value
     * @return string
     */
    public static function key(string $value): FinanceCategory
    {
        return match ($value) {
            'TRANSPORT' => self::TRANSPORT,
            'BILLS' => self::BILLS,
            'FOOD' => self::FOOD,
            'OTHER' => self::OTHER,
            default => self::OTHER,
        };
    }

}