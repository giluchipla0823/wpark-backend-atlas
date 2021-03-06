<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\MailResetPasswordNotification;

/**
 *
 * @OA\Schema(
 * required={"name", "email", "username", "password", "first_login", "online"},
 * @OA\Xml(name="User"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=75, description="Nombre del usuario", example="Javier"),
 * @OA\Property(property="surname", type="string", maxLength=255, description="Apellidos del usuario", example="Garrido Barroso"),
 * @OA\Property(property="email", type="string", maxLength=255, description="Email del usuario", example="jgbacerca@gmail.com"),
 * @OA\Property(property="username", type="string", maxLength=255, description="Nombre de usuario", example="javier.garrido"),
 * @OA\Property(property="password", type="string", maxLength=100, description="Contraseña del usuario", example="jgbTest6;"),
 * @OA\Property(property="remember_token", type="string", maxLength=100, description="Token generado tras usar el servicio de recordar contraseña", example="725br4gOdlqpR6dfGq9HhZrUelCvHAPMD4lxUen1D3jd91ZQRSqHTxZDH6rV"),
 * @OA\Property(property="first_login", type="boolean", maxLength=1, description="Indica si el usuario ha entrado ya en la aplicación (0: No ha entrado, 1: Ya ha entrado)", example="1"),
 * @OA\Property(property="last_login", type="string", format="date-time", description="Fecha y hora de la última vez que el usuario ha entrado en la aplicación", example="2021-10-09 11:20:01"),
 * @OA\Property(property="online", type="boolean", maxLength=1, description="Indica si el usuario está conectado (0: No conectado, 1: Conectado)", example="0"),
 * @OA\Property(property="last_change_password", type="string", format="date", description="Fecha del último cambio de contraseña", example="2021-09-09"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class User
 *
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    public const ACCESS_FROM_MOBILE_APP = 'mobile_app';
    public const ACCESS_FROM_WEB_APP = 'web_app';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'username',
        'password',
        'remember_token',
        'first_login',
        'last_login',
        'online',
        'last_change_password',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param int $compoundId
     * @param int|null $deviceId
     * @param array $abilities
     * @return NewAccessToken
     */
    public function createToken(string $name, int $compoundId, ?int $deviceId = null, array $abilities = ['*']): NewAccessToken
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'compound_id' => $compoundId,
            'device_id' => $deviceId,
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }

    public function compounds()
    {
        return $this->belongsToMany(Compound::class, 'users_compounds', 'user_id', 'compound_id')->withTimestamps();
    }

    public function devices()
    {
        return $this->belongsToMany(Device::class, 'users_devices', 'user_id', 'device_id')->withTimestamps();
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'user_id');
    }

    public function recirculations()
    {
        return $this->hasMany(Recirculation::class, 'user_id');
    }

    public function senderNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function recipientNotifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id');
    }

    /**
     * Creación de la url donde se envía el token para hacer el cambio de contraseña.
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = env('APP_URL_FRONT')."?token=" . $token . "&username=". $this->username;
        $this->notify(new MailResetPasswordNotification($url));
    }

}
