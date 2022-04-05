<?php

namespace App\Virtual\Http\Requests\User;

/**
 * @OA\Schema(
 *      title="User Store Request",
 *      description="User Store request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="UserStoreRequest"
 *      ),
 *      required={"name", "email", "username", "password", "password_confirmation"}
 * )
 */
class UserStoreRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     maxLength=75,
     *     description="Nombre del usuario",
     *     example="Javier"
     * )
     */
    public $name;

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
     *     property="password_confirmation",
     *     type="string",
     *     maxLength=100,
     *     description="Contraseña de confirmación del usuario",
     *     example="jgbTest6;"
     * )
     */
    public $password_confirmation;
}
