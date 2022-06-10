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
 *      required={"transport_exit_id", "carrier_id", "vehicles", "license_plate", "compound_id"}
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
     *     property="vehicles",
     *     type="array",
     *     @OA\Items(
     *          @OA\Property(
     *               property="vin",
     *               type="string",
     *               maxLength=17,
     *               minLength=25,
     *               example="NM0GE9E20N1514928",
     *               description="Vin del vehículo."
     *          ),
     *     ),
     *     @OA\Property(
     *          property="route_id",
     *          type="integer",
     *          example=1,
     *          description="Id de la ruta por defecto o alternativa del transportista."
     *     ),
     *     @OA\Items(type="array"),
     *     description="Lista de vehículos a reubicar en la fila"
     * )
     */
    public $vehicles;

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
     *     property="trailer_license_plate",
     *     type="string",
     *     maxLength=25,
     *     minLength=1,
     *     description="Matricula del remolque que acompaña al camión",
     *     example="5263KAZ"
     * )
     */
    public $trailer_license_plate;

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
