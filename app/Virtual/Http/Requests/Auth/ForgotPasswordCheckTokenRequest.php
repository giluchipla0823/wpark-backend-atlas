<?php

namespace App\Virtual\Http\Requests\Auth;

/**
 * @OA\Schema(
 *      title="Forgot Password Check Token Request",
 *      description="Forgot Password Check Token request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ForgotPasswordCheckTokenRequest"
 *      ),
 *      required={"username", "token"}
 * )
 */
class ForgotPasswordCheckTokenRequest
{
    /**
     * @OA\Property(
     *     property="username",
     *     type="string",
     *     maxLength=255,
     *     description="Nombre de usuario",
     *     example="javier.garrido"
     * )
     */
    public $username;

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
