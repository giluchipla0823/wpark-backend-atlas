<?php

namespace App\Helpers;


class QueryHelper
{
    /**
     * @param string $value
     * @return string
     */
    public static function escapeNamespaceClass(string $value): string
    {
        return str_replace("\\", "\\\\\\", $value);
    }
}
