<?php

namespace App\Virtual\Http\Requests\Area;

/**
 * @OA\Schema(
 *      title="Area Store Request",
 *      description="Area Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="AreaStoreRequest"
 *      ),
 *      required={"name", "compound_id", "zone_id"}
 * )
 */
class AreaStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del área",
     *     example="ÁREA PROVISIONAL"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="compound_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la campa a la que pertence el área",
     *     example="1"
     * )
     */
    public $compound_id;

    /**
     * @OA\Property(
     *     property="zone_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la zona a la que pertence el área",
     *     example="1"
     * )
     */
    public $zone_id;

}
