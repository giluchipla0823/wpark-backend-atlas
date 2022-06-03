<?php

namespace App\Helpers;

class QueryParamsHelper
{
    /**
     * @return array
     */
    public static function getIncludesParamFromRequest(): array {
        $includes = request()->query->get('includes');

        if(!$includes){
            return [];
        }

        return explode(',', $includes);
    }

    /**
     * @return bool
     */
    public static function checkIncludeParamDatatables(): bool
    {
        return request()->query->getInt('datatables', 0) === 1;
    }

    /**
     * @return bool
     */
    public static function checkPaginateParam(): bool
    {
        return request()->query->getBoolean('paginate', false);
    }
}
