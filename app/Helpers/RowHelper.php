<?php

namespace App\Helpers;

class RowHelper
{
    /**
     * @param mixed $value
     * @return string
     */
    public static function zeroFill(mixed $value): string
    {
        return str_pad($value, 3, '0', STR_PAD_LEFT);
    }
}
