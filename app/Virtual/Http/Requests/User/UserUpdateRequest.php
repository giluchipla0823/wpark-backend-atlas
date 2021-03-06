<?php

namespace App\Virtual\Http\Requests\User;

/**
 * @OA\Schema(
 *      title="User Update Request",
 *      description="User Update request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="UserUpdateRequest"
 *      ),
 *      required={"name", "surname", "email", "username"}
 * )
 */
class UserUpdateRequest
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
     *     property="surname",
     *     type="string",
     *     maxLength=255,
     *     description="Apellidos del usuario",
     *     example="Garrido Barroso"
     * )
     */
    public $surname;

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

}
