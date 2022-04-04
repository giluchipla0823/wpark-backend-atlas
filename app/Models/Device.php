<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "uuid", "device_type_id", "active"},
 * @OA\Xml(name="Device"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del dispositivo", example="MOB003"),
 * @OA\Property(property="uuid", type="integer", maxLength=10, description="Imei o IP del dispositivo", example="867906036314920"),
 * @OA\Property(property="device_type_id", type="integer", maxLength=20, description="Tipo del dispositivo", example="1"),
 * @OA\Property(property="version", type="string", maxLength=255, description="Versión del dispositivo", example="2.1.5"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el dispositivo está activo (0: No está activo, 1: Está activo)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Device
 *
 */

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'uuid',
        'device_type_id',
        'version',
        'active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function device()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_devices', 'user_id', 'device_id')->withTimestamps();
    }
}
