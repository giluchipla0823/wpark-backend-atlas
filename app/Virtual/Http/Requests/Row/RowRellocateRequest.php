<?php

namespace App\Virtual\Http\Requests\Row;

/**
 * @OA\Schema(
 *      title="Row Rellocate Request",
 *      description="Row Rellocate request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="RowRellocateRequest"
 *      ),
 *      required={"row_vehicles"}
 * )
 */
class RowRellocateRequest
{
    /**
     * @OA\Property(
     *     property="row_vehicles",
     *     type="array",
     *     @OA\Items(
     *          @OA\Property(
     *               property="vehicle_id",
     *               type="integer",
     *               example="1",
     *               description="Id del vehículo a reubicar en fila."
     *          ),
     *          @OA\Property(
     *               property="origin_position",
     *               type="object",
     *               description="Posición de origen del vehículo.",
     *               @OA\Property(
     *                  property="type",
     *                  type="string",
     *                  example="App\Models\Slot",
     *                  description="Tipo de posición de origen del vehículo."
     *               ),
     *               @OA\Property(
     *                  property="id",
     *                  type="integer",
     *                  example=1,
     *                  description="Id de posición de origen del vehículo."
     *               ),
     *               example={
     *                  "type": "App\Models\Slot",
     *                  "id": 1
     *               }
     *          ),
     *          @OA\Property(
     *               property="destination_position",
     *               type="object",
     *               description="Posición de destino del vehículo.",
     *               @OA\Property(
     *                  property="type",
     *                  type="string",
     *                  example="App\Models\Slot",
     *                  description="Tipo de Posición de destino del vehículo."
     *               ),
     *               @OA\Property(
     *                  property="id",
     *                  type="integer",
     *                  example=11,
     *                  description="Id de Posición de destino del vehículo."
     *               ),
     *               example={
     *                  "type": "App\Models\Slot",
     *                  "id": 11
     *               }
     *          ),
     *     ),
     *     @OA\Items(type="array"),
     *     description="Lista de vehículos a reubicar en la fila"
     * )
     */
    public $row_vehicles;

    /**
     * @OA\Property(
     *     property="buffer_vehicles",
     *     type="array",
     *     @OA\Items(
     *          @OA\Property(
     *               property="vehicle_id",
     *               type="integer",
     *               example="1",
     *               description="Id del vehículo a reubicar en buffer."
     *          ),
     *          @OA\Property(
     *               property="origin_position",
     *               type="object",
     *               description="Posición de origen del vehículo.",
     *               @OA\Property(
     *                  property="type",
     *                  type="string",
     *                  example="App\Models\Slot",
     *                  description="Tipo de posición de origen del vehículo."
     *               ),
     *               @OA\Property(
     *                  property="id",
     *                  type="integer",
     *                  example=1,
     *                  description="Id de posición de origen del vehículo."
     *               ),
     *               example={
     *                  "type": "App\Models\Slot",
     *                  "id": 1
     *               }
     *          )
     *     ),
     *     @OA\Items(type="array"),
     *     description="Lista de vehículos a reubicar en la fila"
     * )
     */
    public $buffer_vehicles;
}
