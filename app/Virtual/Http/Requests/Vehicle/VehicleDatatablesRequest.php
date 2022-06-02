<?php

namespace App\Virtual\Http\Requests\Vehicle;

/**
 * @OA\Schema(
 *      title="Vehicle Datatables Request",
 *      description="Vehicle Stage request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="VehicleDatatatblesRequest"
 *      )
 * )
 */
class VehicleDatatablesRequest
{
    /**
     * @OA\Property(
     *     property="state_id",
     *     type="integer",
     *     description="Filtro por estado del vehículo",
     *     example="2"
     * )
     */
    public $state_id;

    /**
     * @OA\Property(
     *     property="rows",
     *     type="string",
     *     description="Filtro por ids de filas donde se encuentran ubicados los vehículos",
     *     example="2,3,4,5"
     * )
     */
    public $rows;

    /**
     * @OA\Property(
     *     property="vins",
     *     type="object",
     *     description="Filtro por vins",
     *     @OA\Property(
     *        property="value",
     *        type="string",
     *        description="Valores de vins separados por comas",
     *        example="DGEHEUDJJDDEUUEEJ,CNNMDJKREEKEK,OORRIIEOEOEIEEIEI"
     *     ),
     *     @OA\Property(
     *        property="filter_type",
     *        type="string",
     *        description="Tipo de filtro: equal|not_equal",
     *        example="equal"
     *     ),
     *     example={
     *       "value": "DGEHEUDJJDDEUUEEJ,CNNMDJKREEKEK,OORRIIEOEOEIEEIEI",
     *       "filter_type": "equal"
     *     }
     * )
     */
    public $vins;

    /**
     * @OA\Property(
     *     property="state_date",
     *     type="string",
     *     description="Filtro por rango de fecha del estado seleccionado en el campo state_id",
     *     example="10/01/2022 - 20/01/2022"
     * )
     */
    public $state_date;
}
