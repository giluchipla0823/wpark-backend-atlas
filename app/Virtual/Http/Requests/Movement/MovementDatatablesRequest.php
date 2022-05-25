<?php

namespace App\Virtual\Http\Requests\Movement;

/**
 * @OA\Schema(
 *      title="Movement Datatables Request",
 *      description="Movement request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="MovementDatatatblesRequest"
 *      )
 * )
 */
class MovementDatatablesRequest
{
    /**
     * @OA\Property(
     *     property="vins",
     *     type="string",
     *     description="Filtro de vins de movimientos",
     *     example="NM0GE9E20N1514928,NM0GE9E20N1514931"
     * )
     */
    public $vins;

    /**
     * @OA\Property(
     *     property="users",
     *     type="string",
     *     description="Filtro de usuarios de movimientos",
     *     example="1,2"
     * )
     */
    public $users;

    /**
     * @OA\Property(
     *     property="origins_parkings",
     *     type="string",
     *     description="Filtro de parkings de origen de movimientos",
     *     example="1,2"
     * )
     */
    public $origins_parkings;

    /**
     * @OA\Property(
     *     property="destinations_parkings",
     *     type="string",
     *     description="Filtro de parkings de destino de movimientos",
     *     example="1"
     * )
     */
    public $destinations_parkings;

    /**
     * @OA\Property(
     *     property="created_at",
     *     type="string",
     *     description="Filtro de rango de fechas de movimientos",
     *     example="18/05/2022 - 23/05/2022"
     * )
     */
    public $created_at;
}
