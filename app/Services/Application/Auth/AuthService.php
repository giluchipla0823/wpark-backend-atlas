<?php

namespace App\Services\Application\Auth;

use App\Models\Device;
use Exception;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
     * @param array $params
     * @return array
     * @throws AuthenticationException
     */
    public function login(array $params): array
    {
        $credentials = [
            "username" => $params["username"],
            "password" => $params["password"],
        ];

        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException("Credenciales de acceso incorrectas.");
        }

        $user = Auth::user();

        $deviceId = null;

        // TODO: Eliminar porque no es necesario esta comprobación.
        if (array_key_exists('access_from', $params) && $params['access_from'] === User::ACCESS_FROM_MOBILE_APP) {
            // $device = $user->devices()->where("uuid", $params["uuid"])->first();
            $device = Device::where("uuid", $params["uuid"])->first();

            if (!$device) {
                throw new AuthenticationException(
                    "El dispositivo no se encuentra registrado. Por favor, comunicarse con el administrador del sistema."
                );
            }

            $deviceId = $device->id;
        }

        $this->updateUserLogin($user);

        $token = $user->createToken(self::TOKEN_KEY, 1, $deviceId)->plainTextToken;

        return $this->handleUserWithTokenResponse($user, $token);
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

        if (!$user) {
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
        $user = User::where('username', $request->get('username'))->first();

        if (!$user) {
            throw new Exception('No es un usuario válido.', Response::HTTP_BAD_REQUEST);
        }

        // $params = $request->only('email', 'password', 'password_confirmation', 'token');
        $params = $request->only('password', 'password_confirmation', 'token');
        $params['email'] = $user->email;

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
     * @param string $token
     * @return array
     */
    private function handleUserWithTokenResponse(User $user, string $token): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'token' => $token,
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
