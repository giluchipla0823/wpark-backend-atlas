<?php

namespace App\Virtual\Http\Requests\Carrier;

/**
 * @OA\Schema(
 *      title="Carrier Match Vins",
 *      description="Carrier Match vins",
 *      type="object",
 *      @OA\Xml(
 *         name="CarrierStoreRequest"
 *      ),
 *      required={"vins"}
 * )
 */
class CarrierMatchRequest
{
    /**
     * @OA\Property(
     *     property="vins",
     *     type="array",
     *     @OA\Items(
     *          type="string",
     *     ),
     *     @OA\Schema(type="array"),
     *     description="List of Vins",
     *     example={"NM0GE9E20N1514928", "NM0GE9E20N1514931"}
     * )
     */
    public $vins;


}
