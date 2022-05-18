<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelHelper
{

    /**
     * @param string $class
     * @return bool
     */
    public static function isEloquentModelInstance(string $class): bool
    {
        return class_exists($class) && new $class instanceof Model;
    }

    /**
     * @param Model $model
     * @param string $property
     * @return bool
     */
    public static function modelHasPropertyResourceType(Model $model, string $property): bool
    {
        return (
            property_exists($model, $property) &&
            new $model->{$property}($model) instanceof JsonResource
        );
    }
}
