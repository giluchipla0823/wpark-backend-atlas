<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "active"},
 * @OA\Xml(name="Transport"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del transporte", example="FACTORY"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el transporte está activo (0: No esta activo, 1: Esta activo)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Transport
 *
 */

class Transport extends Model
{
    use HasFactory, SoftDeletes;

    public const FACTORY = 1;
    public const TRANSPORT_FACTORY_ID = 1;
    public const TRANSPORT_TRAIN_ID = 2;
    public const TRANSPORT_TRUCK_ID = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Mapea el nombre del transporte al identificador necesario para la API de FreightVerify
     *
     * @param string $transportName Nombre en BD del transporte
     */
    public static function getFreightVerifyType(string $transportName) {
        return match(strtoupper($transportName)) {
            'TRUCK' => '01',
            'RAIL' => '02',
            'SHIP' => '03',
            'AIR' => '04'
        };
    }

    public function loads()
    {
        return $this->hasMany(Route::class, 'exit_transport_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'transport_id');
    }

    public function carriers()
    {
        return $this->belongsToMany(Carrier::class, 'transports_carriers', 'transport_id', 'carrier_id')->withTimestamps();
    }
}
