<?php

namespace App\Virtual\Http\Requests\Vehicle;

/**
 * @OA\Schema(
 *      title="Vehicle Stage Request",
 *      description="Vehicle Stage request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="VehicleStageRequest"
 *      ),
 *      required={"stage", "vin", "eoc", "hybrid"}
 * )
 */
class VehicleStageRequest
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
    public $stage;

    /**
     * @OA\Property(
     *     property="vin",
     *     type="string",
     *     maxLength=17,
     *     description="Número de bastidor del vehículo",
     *     example="WF0FXXWPMFKY73028"
     * )
     */
    public $vin;

    /**
     * @OA\Property(
     *     property="eoc",
     *     type="string",
     *     maxLength=255,
     *     description="Identificador único de ford",
     *     example="5S8DQ87FZAFF090N6   WPMFKY73028  YSC B3EB  CPGD5EZJN A337C7B A6E 63  1765  MH 15"
     * )
     */
    public $eoc;

    /**
     * @OA\Property(
     *     property="hybrid",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el vehículo es híbrido (0: No es híbrido, 1: Es híbrido)",
     *     example="1"
     * )
     */
    public $hybrid;
}
