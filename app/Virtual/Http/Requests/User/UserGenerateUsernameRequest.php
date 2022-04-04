<?php

namespace App\Virtual\Http\Requests\User;

/**
 * @OA\Schema(
 *      title="User Generate Username Request",
 *      description="User Generate Username request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="UserGenerateUsernameRequest"
 *      ),
 *      required={"name", "surname"}
 * )
 */
class UserGenerateUsernameRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     description="Nombre del usuario",
     *     example="Javier"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="surname",
     *     type="string",
     *     description="Apellidos del usuario",
     *     example="Valera"
     * )
     */
    public $surname;

}
