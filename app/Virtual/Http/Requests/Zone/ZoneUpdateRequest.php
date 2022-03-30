<?php

namespace App\Virtual\Http\Requests\Zone;

/**
 * @OA\Schema(
 *      title="Zone Update Request",
 *      description="Zone Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ZoneUpdateRequest"
 *      ),
 *      required={"name"}
 * )
 */
class ZoneUpdateRequest
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
