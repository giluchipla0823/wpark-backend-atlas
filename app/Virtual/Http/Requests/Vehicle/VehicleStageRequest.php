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
 *      required={"tracking-date", "lvin", "pvin", "station", "eoc", "destination"}
 * )
 */
class VehicleStageRequest
{
    /**
     * @OA\Property(
     *     property="tracking-date",
     *     type="string",
     *     format="date-time",
     *     description="Fecha y hora de la estación",
     *     example="2021-07-29 16:05:01.111"
     * )
     */
    public $trackingDate;

    /**
     * @OA\Property(
     *     property="lvin",
     *     type="string",
     *     maxLength=17,
     *     description="Número físico de bastidor del vehículo",
     *     example="WF0UXXWPGUMK11563"
     * )
     */
    public $lvin;

    /**
     * @OA\Property(
     *     property="pvin",
     *     type="string",
     *     maxLength=17,
     *     description="Número lógico de bastidor del vehículo",
     *     example="WF0UXXWPGUMK11563"
     * )
     */
    public $pvin;

    /**
     * @OA\Property(
     *     property="station",
     *     type="string",
     *     maxLength=2,
     *     description="Código de la estación",
     *     example="03"
     * )
     */
    public $station;

    /**
     * @OA\Property(
     *     property="eoc",
     *     type="string",
     *     maxLength=80,
     *     description="Identificador único de ford",
     *     example="2AGK   H 397WDKN4 W WPGUMK11563  ZI9 U3GK1GGKIOPKM4R BM KA5M5N KMNW   22H  AZ  L"
     * )
     */
    public $eoc;

    /**
     * @OA\Property(
     * property="manual",
     * type="boolean",
     * maxLength=1,
     * description="Indica si la trama es manual o automática (0: No es manual, 1: Es manual)",
     * example="0")
    */
    public $manual;

    /**
     * @OA\Property(
     *     property="destination",
     *     type="string",
     *     maxLength=3,
     *     description="Código del código de destino",
     *     example="39"
     * )
     */
    public $destination;
}
