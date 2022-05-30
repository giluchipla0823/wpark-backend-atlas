<?php


namespace App\Virtual\Http\Requests\Load;


/**
 * @OA\Schema(
 *      title="Color Store Request",
 *      description="Color Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ColorStoreRequest"
 *      ),
 *      required={"name", "code", "simple_name", "hex"}
 * )
 */
class LoadValidateRequest
{
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

}
