<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name"},
 * @OA\Xml(name="Zone"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la zona", example="CAMPA GENERAL"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Zone
 *
 */

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    public const PLANTA = 1;
    public const PRESORTING = 2;
    public const CAMPA_GENERAL = 3;
    public const EXTERNO = 4;
    public const OVERFLOW = 5;
    public const BUFFER = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class, 'zone_id');
    }

    /**
     * @return bool
     */
    public function checkIsValidForLoad(): bool
    {
        return in_array($this->id, self::getValidZonesForLoads());
    }

    /**
     * @return array
     */
    public static function getValidZonesForLoads(): array
    {
        return [
            Zone::PRESORTING,
            Zone::CAMPA_GENERAL,
            Zone::EXTERNO,
            Zone::OVERFLOW,
            Zone::BUFFER,
        ];
    }

    /**
     * @return array
     */
    public function getParkingTypesAvailable(): array
    {
        switch ($this->id) {
            case Zone::PRESORTING:
                $parkingTypes = [ParkingType::TYPE_ROW];
                break;

            case Zone::CAMPA_GENERAL:
                $parkingTypes = [ParkingType::TYPE_ROW, ParkingType::TYPE_ESPIGA];
                break;

            default:
                $parkingTypes = [ParkingType::TYPE_UNLIMITED];
                break;
        }

        return $parkingTypes;
    }
}
