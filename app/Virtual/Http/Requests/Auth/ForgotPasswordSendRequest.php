<?php

namespace App\Virtual\Http\Requests\Auth;

/**
 * @OA\Schema(
 *      title="Forgot Password Send Request",
 *      description="Forgot Password Send request body data",
 *      type="object",
 *      @OA\Xml(
 *         name="ForgotPasswordSendRequest"
 *      ),
 *      required={"email"}
 * )
 */
class ForgotPasswordSendRequest
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
}
