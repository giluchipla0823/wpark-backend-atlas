<?php

namespace App\Virtual\Http\Requests\Route;

/**
 * @OA\Schema(
 *      title="Route Update Request",
 *      description="Route Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="RouteUpdateRequest"
 *      ),
 *      required={"name", "cdm_code", "origin_compound_id"}
 * )
 */
class RouteUpdateRequest
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
     *     property="cdm_code",
     *     type="string",
     *     maxLength=5,
     *     description="Código de la ruta",
     *     example="AEP"
     * )
     */
    public $cdm_code;

    /**
     * @OA\Property(
     *     property="carrier_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la empresa de transporte que hace la ruta",
     *     example="1")
     */
    public $carrier_id;

     /**
     * @OA\Property(
     *     property="transport_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la método de transporte de la ruta",
     *     example="1")
     */
    public $transport_id;

     /**
     * @OA\Property(
     *     property="destination_code_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica el código de destino de la ruta",
     *     example="1")
     */
    public $destination_code_id;

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
