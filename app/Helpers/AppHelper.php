<?php

namespace App\Helpers;

class AppHelper
{
    /**
     * @param float $percentage
     * @return string
     */
    public static function getFillTypeToParkingOrRow(float $percentage): string
    {
        $type = 'empty';

        if ($percentage > 80) {
            $type = 'almost-full';
        } else if ($percentage > 60 && $percentage < 80) {
            $type = 'more-than-half-full';
        } else if ($percentage > 0 && $percentage < 60) {
            $type = 'half-full';
        }

        return $type;
    }
}
