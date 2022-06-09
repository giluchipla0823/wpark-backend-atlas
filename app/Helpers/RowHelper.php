<?php

namespace App\Helpers;

use App\Models\Row;
use App\Models\Zone;

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

    /**
     * @param Row $row
     * @return bool
     */
    public static function isPresortinZone(Row $row): bool
    {
        return $row->parking->area->zone->id === Zone::PRESORTING;
    }
}
