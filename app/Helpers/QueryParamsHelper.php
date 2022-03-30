<?php

namespace App\Helpers;

class QueryParamsHelper
{
    /**
     * @return bool
     */
    public static function checkIncludeParamDatatables(): bool
    {
        return request()->query->getInt('datatables', 0) === 1;
    }
}
