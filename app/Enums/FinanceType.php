<?php

namespace App\Enums;

enum FinanceType: string
{
    case INCOME = 'Income';
    case EXPENSE = 'Expense';
    

    /**
     * Get all the values of the enum
     *
     * @return array
     */
    public static function values(): array
    {
        return [
            self::INCOME->value,
            self::EXPENSE->value,
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
            'INCOME',
            'EXPENSE'
        ];
    }

    /**
     * Get the key of the enum based on the value
     *
     * @param string $value
     * @return string
     */
    public static function key(string $value): FinanceType
    {
        return match ($value) {
            'INCOME' => self::INCOME,
            'EXPENSE' => self::EXPENSE,
            default => self::INCOME,
        };
    }

}