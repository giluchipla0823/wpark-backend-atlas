<?php

namespace App\Virtual\Http\Requests\Parking;

/**
 * @OA\Schema(
 *      title="Parking Update Request",
 *      description="Parking Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ParkingUpdateRequest"
 *      ),
 *      required={"name", "active"}
 * )
 */
class ParkingUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del parking",
     *     example="PU"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el parking está activo (0: No está activo, 1: Está activo)",
     *     example="1"
     * )
     */
    public $active;

    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre el parking",
     *     example="Este parking deberá cambiar de ubicación"
     * )
     */
    public $comments;
}
