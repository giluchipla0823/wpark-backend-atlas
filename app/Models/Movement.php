<?php

namespace App\Models;

use App\Http\Resources\Parking\ParkingResource;
use App\Http\Resources\Slot\SlotResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @OA\Schema(
 * required={"vehicle_id", "user_id", "compound_id", "origin_position_id", "destination_position_id", "rule_id", "dt_start", "dt_end"},
 * @OA\Xml(name="Movement"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="vehicle_id", type="integer", maxLength=20, description="Indica el vehículo que se mueve", example="1"),
 * @OA\Property(property="user_id", type="integer", maxLength=20, description="Indica el usuario que está moviendo el vehículo", example="1"),
 * @OA\Property(property="device_id", type="integer", maxLength=20, description="Indica el dispositivo que ha usado el usuario para realizar el movimiento del vehículo", example="1"),
 * @OA\Property(property="origin_position_type", type="string", maxLength=255, description="Indica el tipo de posición slot o parking de origen", example="App\Models\Parking"),
 * @OA\Property(property="origin_position_id", type="integer", maxLength=20, description="Indica la posición desde donde se hace el movimiento", example="1"),
 * @OA\Property(property="destination_position_type", type="string", maxLength=255, description="Indica el tipo de posición slot o parking de destino", example="App\Models\Slot"),
 * @OA\Property(property="destination_position_id", type="integer", maxLength=20, description="Indica la posición haciá donde se hace el movimiento", example="2"),
 * @OA\Property(property="category", type="string", maxLength=255, description="Nombre de la categoría (shipping_rule_id) que se aplica en ese movimiento", example="FLUSHING"),
 * @OA\Property(property="confirmed", type="boolean", maxLength=1, description="Indica si el movimiento se ha confirmado (0: No está confirmado, 1: Está confirmado)", example="1"),
 * @OA\Property(property="canceled", type="boolean", maxLength=1, description="Indica si el movimiento se ha cancelado (0: No está cancelado, 1: Está cancelado)", example="0"),
 * @OA\Property(property="manual", type="boolean", maxLength=1, description="Indica si el movimiento es el recomendado o manual (0: Recomendado, 1: Manual)", example="0"),
 * @OA\Property(property="dt_start", type="string", format="date-time", description="Fecha y hora del comienzo del movimiento", example="2021-12-09 11:20:01"),
 * @OA\Property(property="dt_end", type="string", format="date-time", description="Fecha y hora del final del movimiento", example="2021-12-12 11:26:15"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre la fila", example="Movimiento cancelado por bloqueo"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Movement
 *
 */
class Movement extends Model
{
    use HasFactory, SoftDeletes;

    public const MOVEMENT_ACTION_OK = 1;
    public const MOVEMENT_ACTION_NO_OK = 2;
    public const MOVEMENT_ACTION_SALES = 3;
    public const MOVEMENT_ACTION_ESCAPE = 4;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'device_id',
        'origin_position_id',
        'origin_position_type',
        'destination_position_id',
        'destination_position_type',
        'category',
        'confirmed',
        'canceled',
        'manual',
        'dt_start',
        'dt_end',
        'comments',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $user = Auth::user();

            $token = $user ? $user->currentAccessToken() : null;

            $deviceId = null;

            if ($token) {
                $deviceId = $token->device ? $token->device->id : null;
            }

            $model->device_id = $deviceId;
        });
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function originPosition()
    {
        return $this->morphTo(__FUNCTION__, 'origin_position_type', 'origin_position_id');
    }

    public function destinationPosition()
    {
        return $this->morphTo(__FUNCTION__, 'destination_position_type', 'destination_position_id');
    }

    public function resolvePositionResource(string $type): string
    {
        return $type === Parking::class
            ? ParkingResource::class
            : SlotResource::class;
    }

    public function originPositionIsSlot(): bool
    {
        return $this->origin_position_type === Slot::class;
    }

    public function originPositionIsParking(): bool
    {
        return $this->origin_position_type === Parking::class;
    }

    public function destinationPositionIsSlot(): bool
    {
        return $this->destination_position_type === Slot::class;
    }

    public function destinationPositionIsParking(): bool
    {
        return $this->destination_position_type === Parking::class;
    }
}
