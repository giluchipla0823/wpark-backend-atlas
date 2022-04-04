<?php

namespace App\Virtual\Http\Requests\Stage;

/**
 * @OA\Schema(
 *      title="Stage Store Request",
 *      description="Stage Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="StageStoreRequest"
 *      ),
 *      required={"name", "short_name"}
 * )
 */
class StageStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la etapa",
     *     example="STAGE 3"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="short_name",
     *     type="string",
     *     maxLength=5,
     *     description="Nombre corto de la etapa",
     *     example="ST3"
     * )
     */
    public $short_name;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     maxLength=255,
     *     description="Descripción de la etapa",
     *     example="Etapa de creación del vehículo"
     * )
     */
    public $description;
}
