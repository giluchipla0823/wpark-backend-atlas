<?php

namespace App\Virtual\Responses\Unauthorized;

use App\Virtual\Responses\BaseJsonContent;

/**
 * @OA\Schema(
 *     title="Unauthorized Json",
 *     description="Unauthorized json response content",
 *     @OA\Xml(
 *         name="UnauthorizedJson"
 *     )
 * )
 */
class UnauthorizedJsonContent extends BaseJsonContent
{
    /**
     * @OA\Property(
     *      property="code",
     *      type="integer",
     *      example="401"
     * )
     */
    public $code;

    /**
     * @OA\Property(
     *      property="message",
     *      type="string",
     *      example="Unauthorized."
     * )
     */
    public $message;
}
