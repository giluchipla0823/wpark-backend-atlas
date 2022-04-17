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
        $category = 'almost-full';

        if ($percentage > 60 && $percentage < 80) {
            $category = 'more-than-half-full';
        } else if ($percentage < 60) {
            $category = 'half-full';
        }

        return $category;
    }
}
