<?php

namespace App\Helpers;

class ClassHelper
{
    /**
     * @param string $className
     * @param string $constantName
     * @return bool
     */
    public static function hasConstant(string $className, string $constantName): bool
    {
        return defined("{$className}::{$constantName}");
    }
}
