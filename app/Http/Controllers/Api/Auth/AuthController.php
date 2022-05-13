<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\ForgotPasswordResetRequest;
use App\Http\Requests\Auth\ForgotPasswordSendRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Application\Auth\AuthService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="User Login",
     *     description="Login User Here",
     *     operationId="authLogin",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *      @OA\Response(response=200, description="Login Successfully"),
     *      @OA\Response(response=400, ref="#/components/responses/Authentication"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->authService->login($request->all());

        return $this->successResponse($response);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/logout",
     *      tags={"Auth"},
     *      summary="User Logout",
     *      description="Logout User Here",
     *      security={{"sanctum": {}}},
     *      operationId="authLogout",
     *      @OA\Response(response=200, description="Logout Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse // TODO: En swagger no borra el token, devuelve un error 500 (Call to undefined method Laravel\\Sanctum\\TransientToken::delete()), en postman si funciona
    {
        $this->authService->logout($request->user());

        return $this->showMessage('Se ha cerrado la sesión del usuario.');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/reset-password",
     *     tags={"Auth"},
     *     summary="User Reset Password",
     *     description="Reset Password",
     *     security={{"sanctum": {}}},
     *     operationId="authResetPassword",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
     *     ),
     *      @OA\Response(response=200, description="Reset Password Successfully"),
     *      @OA\Response(response=400, ref="#/components/responses/Authentication"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword($request->all());

        return $this->showMessage('Se ha cambiado la contraseña del usuario.');
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     tags={"Auth"},
     *     summary="Forgot Password Send",
     *     description="Forgot Password Send Email",
     *     operationId="authForgotPasswordSend",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ForgotPasswordSendRequest")
     *     ),
     *      @OA\Response(response=200, description="Email Send Successfully"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param ForgotPasswordSendRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function forgotPasswordSend(ForgotPasswordSendRequest $request): JsonResponse
    {
        $this->authService->forgotPasswordSend($request);

        return $this->showMessage("El email ha sido enviado con éxito.");
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password-check",
     *     tags={"Auth"},
     *     summary="Forgot Password Check",
     *     description="Forgot Password Check Token",
     *     operationId="authForgotPasswordCheckToken",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ForgotPasswordCheckTokenRequest")
     *     ),
     *      @OA\Response(response=200, description="Token validate Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param ForgotPasswordResetRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function forgotPasswordCheckToken(Request $request): JsonResponse
    {
        $this->authService->forgotPasswordCheckToken($request);

        return $this->showMessage("Token de recuperación de contraseña válido.");
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password-reset",
     *     tags={"Auth"},
     *     summary="Forgot Password Reset",
     *     description="Forgot Password Reset",
     *     operationId="authForgotPasswordReset",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ForgotPasswordResetRequest")
     *     ),
     *      @OA\Response(response=200, description="Email Reset Successfully"),
     *      @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param ForgotPasswordResetRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function forgotPasswordReset(ForgotPasswordResetRequest $request): JsonResponse
    {
        $this->authService->forgotPasswordReset($request);

        return $this->showMessage("Se ha cambiado la contraseña del usuario.");
    }
}
