<?php

namespace App\Virtual\Http\Requests\Auth;

/**
 * @OA\Schema(
 *      title="Login Request",
 *      description="Login request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="LoginRequest"
 *      ),
 *      required={"username", "password"}
 * )
 */
class LoginRequest
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
     *     property="password",
     *     type="string",
     *     maxLength=100,
     *     description="Contraseña del usuario",
     *     example="jgbTest6;"
     * )
     */
    public $password;
}
