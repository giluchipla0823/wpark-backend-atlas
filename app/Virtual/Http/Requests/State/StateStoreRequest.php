<?php

namespace App\Virtual\Http\Requests\State;

/**
 * @OA\Schema(
 *      title="State Store Request",
 *      description="State Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="StateStoreRequest"
 *      ),
 *      required={"name"}
 * )
 */
class StateStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del estado",
     *     example="STATIONED"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     maxLength=255,
     *     description="Descripción del estado",
     *     example="El vehículo está estacionado"
     * )
     */
    public $description;

}
