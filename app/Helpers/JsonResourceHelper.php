<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\JsonResource;
use ReflectionClass;

class JsonResourceHelper
{

    /**
     * @param string $class
     * @return bool
     */
    public static function isInstance(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        return (new ReflectionClass($class))->getConstructor()->class === JsonResource::class;
    }
}
