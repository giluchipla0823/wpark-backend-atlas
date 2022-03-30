<?php

namespace App\Virtual\Http\Requests\Auth;

/**
 * @OA\Schema(
 *      title="Forgot Password Reset Request",
 *      description="Forgot Password Reset request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ForgotPasswordResetRequest"
 *      ),
 *      required={"email", "password", "password_confirmation", "token"}
 * )
 */
class ForgotPasswordResetRequest
{
    /**
     * @OA\Property(
     *     property="email",
     *     type="string",
     *     maxLength=255,
     *     description="Email del usuario",
     *     example="jgbacerca@gmail.com"
     * )
     */
    public $email;

    /**
     * @OA\Property(
     *     property="password",
     *     type="string",
     *     maxLength=255,
     *     description="Contraseña nueva del usuario",
     *     example="test"
     * )
     */
    public $password;

    /**
     * @OA\Property(
     *     property="password_confirmation",
     *     type="string",
     *     maxLength=255,
     *     description="Confirmación de la contraseña nueva del usuario",
     *     example="test"
     * )
     */
    public $password_confirmation;

    /**
     * @OA\Property(
     *     property="token",
     *     type="string",
     *     maxLength=255,
     *     description="Token para cambio de contraseña",
     *     example="70acaa7417a2f743df80e040a40e67fd386ddc7eb1a477dfe59e6dc52ebcce46"
     * )
     */
    public $token;
}
