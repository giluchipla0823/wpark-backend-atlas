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
     *     property="states",
     *     type="string",
     *     description="Filtro de estados de vehículos",
     *     example="2,3,4"
     * )
     */
    public $states;
}
