<?php

namespace App\Virtual\Responses;

class BaseJsonContent
{
    /**
     * @OA\Property(
     *     property="jsonapi",
     *     type="object",
     *     ref="#/components/schemas/JsonApiDefinition"
     * )
     */
    public $jsonapi;
}
