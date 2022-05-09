<?php

namespace App\Virtual\Http\Requests\Design;

/**
 * @OA\Schema(
 *      title="Design Update Request",
 *      description="Design Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="DesignUpdateRequest"
 *      ),
 *      required={"name", "code", "brand_id", "length", "width", "height", "weight", "description", "manufacturing"}
 * )
 */
class DesignUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del modelo",
     *     example="KUGA 2021"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="short_name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre corto del modelo",
     *     example="KUGA"
     * )
     */
    public $short_name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=25,
     *     description="Código del modelo",
     *     example="C250"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *     property="brand_id",
     *     type="integer",
     *     maxLength=20,
     *     description="Indica la marca a la que pertence el modelo",
     *     example="1"
     * )
     */
    public $brand_id;

    /**
     * @OA\Property(
     *     property="length",
     *     type="integer",
     *     maxLength=10,
     *     description="Longitud del modelo",
     *     example="4614"
     * )
     */
    public $length;

    /**
     * @OA\Property(
     *     property="width",
     *     type="integer",
     *     maxLength=10,
     *     description="Anchura del modelo",
     *     example="1882"
     * )
     */
    public $width;

    /**
     * @OA\Property(
     *     property="height",
     *     type="integer",
     *     maxLength=10,
     *     description="Altura del modelo",
     *     example="1661"
     * )
     */
    public $height;

    /**
     * @OA\Property(
     *     property="weight",
     *     type="integer",
     *     maxLength=10,
     *     description="Peso del modelo",
     *     example="1580"
     * )
     */
    public $weight;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     maxLength=255,
     *     description="Descripción del modelo",
     *     example="Kuga new"
     * )
     */
    public $description;

    /**
     * @OA\Property(
     *     property="manufacturing",
     *     type="boolean",
     *     maxLength=1,
     *     description="Indica si el modelo está fabricado (0: No fabricado, 1: Fabricado)",
     *     example="1"
     * )
     */
    public $manufacturing;

    /**
     * @OA\Property(
     *     property="svg",
     *     type="string",
     *     description="Imagen del modelo",
     *     example="<svg>...</svg>"
     * )
     */
    public $svg;
}
