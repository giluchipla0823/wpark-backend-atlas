<?php

namespace App\Virtual\Definitions;

/**
 * @OA\Schema(
 *     title="ValidationErrorsDefinition",
 *     description="ValidationErrorsDefinition",
 *     @OA\Xml(
 *         name="ValidationErrorsDefinition"
 *     )
 * )
 */
class ValidationErrorsDefinition
{
    /**
     * @OA\Property(
     *   property="field",
     *   type="string",
     *   example=""
     * ),
     */
    public $field;

    /**
     * @OA\Property(
     *   property="message",
     *   type="string",
     *   example=""
     * )
     */
    public $message;
}
