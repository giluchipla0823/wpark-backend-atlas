<?php

namespace App\Virtual\Http\Requests\Auth;

/**
 * @OA\Schema(
 *      title="Reset Password Request",
 *      description="Reset Password request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ResetPasswordRequest"
 *      ),
 *      required={"password", "newPassword", "confirmPassword"}
 * )
 */
class ResetPasswordRequest
{
    /**
     * @OA\Property(
     *     property="password",
     *     type="string",
     *     maxLength=100,
     *     description="Contraseña del usuario",
     *     example="jgbTest6;"
     * )
     */
    public $password;

    /**
     * @OA\Property(
     *     property="newPassword",
     *     type="string",
     *     maxLength=255,
     *     description="Contraseña nueva del usuario",
     *     example="test"
     * )
     */
    public $newPassword;

    /**
     * @OA\Property(
     *     property="confirmPassword",
     *     type="string",
     *     maxLength=255,
     *     description="Confirmación de la contraseña nueva del usuario",
     *     example="test"
     * )
     */
    public $confirmPassword;
}
