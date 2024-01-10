<?php

namespace App\Helper;


class Currency
{
    public static function rupiah(int $value): string
    {
        return 'Rp. ' . number_format($value, 0, ',', '.');
    }
}