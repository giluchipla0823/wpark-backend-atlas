<?php

namespace App\Virtual\Http\Requests\Stage;

/**
 * @OA\Schema(
 *      title="Stage Update Request",
 *      description="Stage Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="StageUpdateRequest"
 *      ),
 *      required={"name", "code"}
 * )
 */
class StageUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la etapa",
     *     example="St3"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=5,
     *     description="Código de la etapa",
     *     example="03"
     * )
     */
    public $code;

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
