<?php

namespace App\Virtual\Http\Requests\Route;

// TODO: Añadir carrier_id y dealer_id en fase 2
/**
 * @OA\Schema(
 *      title="Route Store Request",
 *      description="Route Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="RouteStoreRequest"
 *      ),
 *      required={"name", "code", "origin_compound_id", "destination_compound_id"}
 * )
 */
class RouteStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de la ruta",
     *     example="ANTWERP CHINA"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=5,
     *     description="Código de la ruta",
     *     example="ANTC1"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="origin_compound_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la campa de origen",
     *     example="1"
     * )
     */
    public $origin_compound_id;

    /**
     * @OA\Property(
     *     property="destination_compound_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la campa de destino",
     *     example="2"
     * )
     */
    public $destination_compound_id;

    /**
     * @OA\Property(
     *     property="comments",
     *     type="string",
     *     description="Comentarios sobre la ruta",
     *     example="Esta ruta actualmente tiene un desvío"
     * )
     */
    public $comments;
}
