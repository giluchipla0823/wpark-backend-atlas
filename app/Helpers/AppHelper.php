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
        $category = 'empty';

        if ($percentage > 80) {
            $category = 'almost-full';
        } else if ($percentage > 60 && $percentage < 80) {
            $category = 'more-than-half-full';
        } else if ($percentage > 0 && $percentage < 60) {
            $category = 'half-full';
        }

        return $category;
    }
}
