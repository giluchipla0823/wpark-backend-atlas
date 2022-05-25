<?php

namespace App\Helpers;

use Carbon\Carbon;

class DatesHelper
{

    /**
     * @param string $value
     * @param string $format
     * @return string[]
     */
    public static function getFormattedRangeDates(string $value, string $format = 'd/m/Y'): array
    {
        list($startDate, $endDate) = explode("-", str_replace(' ', '', $value));

        return [
            'start_date' => Carbon::createFromFormat($format, $startDate)->toDateString() . " 00:00:00",
            'end_date' => Carbon::createFromFormat($format, $endDate)->toDateString() . " 23:59:59"
        ];
    }
}
