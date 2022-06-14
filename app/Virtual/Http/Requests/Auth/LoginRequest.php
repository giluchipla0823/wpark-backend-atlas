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

    /**
     * @OA\Property(
     *     property="uuid",
     *     type="string",
     *     description="UUID del dispositivo con el que accede el usuario. Este campo es requerido cuando el campo access_from es mobile_app",
     *     example="1abcde230"
     * )
     */
    public $uuid;

    /**
     * @OA\Property(
     *     property="access_from",
     *     type="string",
     *     description="Desde que aplicación accede el usuario: web_app o mobile_app",
     *     example="web_app"
     * )
     */
    public $access_from;
}
