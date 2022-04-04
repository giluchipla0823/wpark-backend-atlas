<?php

namespace App\Virtual\Http\Requests\Country;

/**
 * @OA\Schema(
 *      title="Country Update Request",
 *      description="Country Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="CountryUpdateRequest"
 *      ),
 *      required={"name", "code"}
 * )
 */
class CountryUpdateRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre del país",
     *     example="GERMANY"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="code",
     *     type="string",
     *     maxLength=255,
     *     description="Código del país",
     *     example="GK"
     * )
     */
    public $code;
}
