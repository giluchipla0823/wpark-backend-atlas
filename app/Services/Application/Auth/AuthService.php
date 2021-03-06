<?php

namespace App\Services\Application\Auth;

use Exception;
use App\Models\User;
use App\Models\Compound;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\owner\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Device\DeviceRepositoryInterface;

class AuthService
{
    private const TOKEN_KEY = 'auth_token';

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var DeviceRepositoryInterface
     */
    private $deviceRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        DeviceRepositoryInterface $deviceRepository
    ) {
        $this->userRepository = $userRepository;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param array $params
     * @return array
     * @throws BadRequestException
     */
    public function login(array $params): array
    {
        $credentials = [
            "username" => $params["username"],
            "password" => $params["password"],
        ];

        if (!Auth::attempt($credentials)) {
            throw new BadRequestException("Credenciales de acceso incorrectas.");
        }

        $user = Auth::user();

        $deviceId = null;

        if (array_key_exists('access_from', $params) && $params['access_from'] === User::ACCESS_FROM_MOBILE_APP) {
            $uuid = $params["uuid"];

            $device = $this->deviceRepository->findOneByUuid($uuid);

            if (!$device) {
                throw new BadRequestException(
                    "El dispositivo no se encuentra registrado. Por favor, comunicarse con el administrador del sistema.",
                    [
                        "error_details" => [
                            "reference_code" => "NOT_FOUND_DEVICE",
                            "data" => ["uuid" => $uuid]
                        ]
                    ]
                );
            }

            $deviceId = $device->id;

            $personalAccessToken = PersonalAccessToken::where([
                ["tokenable_type", "=", User::class],
                ["tokenable_id", "=", $user->id],
                ["device_id", "=", $deviceId],
            ])->first();

            if ($personalAccessToken) {
                throw new BadRequestException(
                    "Ya existe una sesi??n iniciada con el dispositivo especificado.",
                    [
                        "error_details" => [
                            "reference_code" => "DUPLICATE_DEVICE_AUTHENTICATION",
                            "data" => [
                                "device" => [
                                    "name" => $device->name,
                                    "uuid" => $device->uuid,
                                ],
                                "user" => [
                                    "username" => $user->username
                                ]
                            ]
                        ]
                    ]
                );
            }
        }

        $this->updateUserLogin($user);

        $token = $user->createToken(self::TOKEN_KEY, Compound::VALENCIA_ID, $deviceId);

        return $this->handleUserWithTokenResponse($user, $token->plainTextToken);
    }

    /**
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $params['online'] = 0;

        $this->userRepository->update($params, $user->id);

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

        $this->userRepository->update($params, $user->id);
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function forgotPasswordSend(Request $request): void
    {
        if (!User::where('email', $request->email)->exists()) {
            throw new Exception("No existe ning??n usuario con ese email.", Response::HTTP_BAD_REQUEST);
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
            throw new Exception('No es un usuario v??lido.', Response::HTTP_UNAUTHORIZED);
        }

        $status = Password::tokenExists($user, $token);

        if (!$status) {
            throw new Exception('Su token de recuperaci??n de contrase??a no es v??lido.', Response::HTTP_UNAUTHORIZED);
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
            throw new Exception('No es un usuario v??lido.', Response::HTTP_BAD_REQUEST);
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
     * Actualiza los datos de la primera conexi??n, ??ltima conexi??n y online al loguear.
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

        $this->userRepository->update($params, $user->id);
    }

}
