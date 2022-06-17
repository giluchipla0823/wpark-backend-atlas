<?php

namespace App\Helpers;

class StringHelper
{
    public static function replaceLastOccurrence(string $search , string $replace , string $str): string
    {
        if( ( $pos = strrpos( $str , $search ) ) !== false ) {
            $search_length  = strlen( $search );
            $str    = substr_replace( $str , $replace , $pos , $search_length );
        }
        return $str;
    }
}
