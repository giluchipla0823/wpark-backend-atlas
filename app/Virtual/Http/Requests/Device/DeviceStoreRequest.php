<?php

namespace App\Virtual\Http\Requests\Device;

/**
 * @OA\Schema(
 *      title="Device Store Request",
 *      description="Device Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="DeviceStoreRequest"
 *      ),
 *      required={"name", "uuid", "device_type_id"}
 * )
 */
class DeviceStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del dispositivo",
     *     example="Iphone de Alguien"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="uuid",
     *     type="string",
     *     maxLength=255,
     *     description="UUID del dispositivo",
     *     example="0002222556666"
     * )
     */
    public $uuid;

    /**
     * @OA\Property(
     *     property="device_type_id",
     *     type="integer",
     *     description="Tipo de dispositivo",
     *     example=1
     * )
     */
    public $device_type_id;

    /**
     * @OA\Property(
     *     property="version",
     *     type="string",
     *     description="Versión del dispositivo",
     *     example=null
     * )
     */
    public $version;
}
