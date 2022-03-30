<?php

namespace App\Services\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\AuthenticationException;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    private const TOKEN_KEY = 'auth_token';

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param array $credentials
     * @return array
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException("Credenciales de acceso incorrectas.");
        }

        $user = Auth::user();

        $this->updateUserLogin($user);

        return $this->handleUserWithTokenResponse($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $params['online'] = 0;

        $this->repository->update($params, $user->id);

        $user->currentAccessToken()->delete();
    }

    /**
     * @param array $data
     * @return void
     * @throws AuthenticationException
     */
    public function resetPassword(array $data): void
    {
        $user = Auth::user();

        if (!Hash::check($data['password'], $user->getAuthPassword())) {
            throw new AuthenticationException();
        }

        $params['password'] = $data['newPassword'];
        $params['last_change_password'] = date("Y-m-d H:i:s", strtotime('now'));

        $this->repository->update($params, $user->id);
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function forgotPasswordSend(Request $request): void
    {
        if (!User::where('email', $request->email)->exists()) {
            throw new Exception("No existe ningún usuario con ese email.", Response::HTTP_BAD_REQUEST);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw new Exception(__($status), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function forgotPasswordCheckToken(Request $request): void
    {
        $user = User::where('username', $request->username)->first();
        $token = $request->token;

        if(!$user){
            throw new Exception('No es un usuario válido.', Response::HTTP_UNAUTHORIZED);
        }

        $status = Password::tokenExists($user, $token);

        if (!$status) {
            throw new Exception('Su token de recuperación de contraseña no es válido.', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function forgotPasswordReset(Request $request): void
    {
        $params = $request->only('email', 'password', 'password_confirmation', 'token');

        $status = Password::reset($params, function($user, $password){
            $user->forceFill([
                'password' => Hash::make($password),
                'last_change_password' => date("Y-m-d H:i:s", strtotime('now'))
            ])->setRememberToken(Str::random(60));

            $user->save();
            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw new Exception(__($status), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param User $user
     * @return array
     */
    private function handleUserWithTokenResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'token' => $user->createToken(self::TOKEN_KEY)->plainTextToken,
        ];
    }

    /**
     * Actualiza los datos de la primera conexión, última conexión y online al loguear.
     *
     * @param User $user
     * @return void
     */
    private function updateUserLogin(User $user): void
    {
        if ($user->first_login === 0) {
            $params['first_login'] = 1;
        }

        $params['last_login'] = date("Y-m-d H:i:s", strtotime('now'));
        $params['online'] = 1;

        $this->repository->update($params, $user->id);
    }

}
