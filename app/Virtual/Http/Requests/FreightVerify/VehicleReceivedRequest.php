<?php

namespace App\Virtual\Http\Requests\FreightVerify;

/**
 * @OA\Schema(
 *      title="FreightVerify Vehicle Received Request",
 *      description="Send Vehicle Received Milestone to FreightVerify API",
 *      type="object",
 *      @OA\Xml(
 *         name="HoldStoreRequest"
 *      ),
 *     required={"vin","transportationType","senderName","scac","ms1LocationCode","ms1StateOrProvinceCode","ms1CountryCode","compoundCode","yardCode","bayCode","nextCarrier","equipmentType","equipmentNumber","voyageNumber","assetId"}
 * )
 */
class VehicleReceivedRequest
{
    /**
     * @OA\Property(
     *     property="vin",
     *     type="string",
     *     maxLength=255,
     *     description="VIN",
     * )
     */
    public $vin;
    /**
     * @OA\Property(
     *     property="transportationType",
     *     type="string",
     *     enum={"01", "02", "03", "04"},
     *     maxLength=2,
     *     description="Mode types are defined as: 01 – Truck, 02 – Rail, 03 – Ship, 04 – Air",
     * )
     */
    public $transportationType;
    /**
     * @OA\Property(
     *     property="senderName",
     *     type="string",
     *     maxLength=255,

     * )
     */
    public $senderName;
    /**
     * @OA\Property(
     *     property="scac",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $scac;
    /**
     * @OA\Property(
     *     property="ms1LocationCode",
     *     type="string",
     *     maxLength=255,

     * )
     */
    public $ms1LocationCode;
    /**
     * @OA\Property(
     *     property="ms1StateOrProvinceCode",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $ms1StateOrProvinceCode;
    /**
     * @OA\Property(
     *     property="ms1CountryCode",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $ms1CountryCode;
    /**
     * @OA\Property(
     *     property="compoundCode",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $compoundCode;
    /**
     * @OA\Property(
     *     property="yardCode",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $yardCode;
    /**
     * @OA\Property(
     *     property="bayCode",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $bayCode;
    /**
     * @OA\Property(
     *     property="nextCarrier",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $nextCarrier;
    /**
     * @OA\Property(
     *     property="equipmentType",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $equipmentType;
    /**
     * @OA\Property(
     *     property="equipmentNumber",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $equipmentNumber;
    /**
     * @OA\Property(
     *     property="voyageNumber",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $voyageNumber;
    /**
     * @OA\Property(
     *     property="assetId",
     *     type="string",
     *     maxLength=255,
     * )
     */
    public $assetId;
}
