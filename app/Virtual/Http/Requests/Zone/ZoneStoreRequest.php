<?php

namespace App\Virtual\Http\Requests\Zone;

/**
 * @OA\Schema(
 *      title="Zone Store Request",
 *      description="Zone Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ZoneStoreRequest"
 *      ),
 *      required={"name"}
 * )
 */
class ZoneStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la zona",
     *     example="CAMPA GENERAL"
     * )
     */
    public $name;
}
