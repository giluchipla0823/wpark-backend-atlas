<?php


namespace App\Virtual\Http\Requests;

/**
 * @OA\Schema(
 *      title="Color Store Request",
 *      description="Color Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ColorStoreRequest"
 *      ),
 *      required={"transport_exit_id", "transport_identifier", "carrier_id", "vins", "license_plate", "compound_id"}
 * )
 */
class LoadGenerateRequest
{
    /**
     * @OA\Property(
     *     property="transport_exit_id",
     *     type="integer",
     *     maxLength=20,
     *     minLength=20,
     *     description="ID del transporte de salida",
     *     example="3 → TRUCT"
     * )
     */
    public $transport_exit_id;

    /**
     * @OA\Property(
     *     property="transport_identifier",
     *     type="string",
     *     maxLength=50,
     *     minLength=1,
     *     description="Identificador de la carga",
     *     example="3 → TRUCT"
     * )
     */
    public $transport_identifier;

    /**
     * @OA\Property(
     *     property="carrier_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Id del transportista",
     *     example="6"
     * )
     */
    public $carrier_id;

    /**
     * @OA\Property(
     *     property="vins",
     *     type="array",
     *      @OA\Items(
     *          type="string",
     *     ),
     *     description="Array de vins de los vehiculos",
     *     example="['NM0GE9E20N1514928', 'NM0GE9E20N1514931']"
     * )
     */
    public $vins;

    /**
     * @OA\Property(
     *     property="license_plate",
     *     type="string",
     *     maxLength=50,
     *     minLength=1,
     *     description="Matricula del camion",
     *     example="5263KAZ"
     * )
     */
    public $license_plate;

    /**
     * @OA\Property(
     *     property="compound_id",
     *     type="string",
     *     maxLength=50,
     *     minLength=1,
     *     description="ID de la campa",
     *     example="2"
     * )
     */
    public $compound_id;
}
