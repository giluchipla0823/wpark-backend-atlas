<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "code"},
 * @OA\Xml(name="Stage"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la estación", example="St3"),
 * @OA\Property(property="code", type="string", maxLength=5, description="Código de la estación", example="03"),
 * @OA\Property(property="description", type="string", maxLength=255, description="Descripción de la estación", example="Stage 3 - Etapa de creación del vehículo"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Stage
 *
 */

class Stage extends Model
{
    use HasFactory, SoftDeletes;

    public const STAGE_VEHICLE_CREATED_ID = 1;
    public const STAGE_GATE_RELEASE_ID = 5;
    public const STAGE_VEHICLE_LEFT_ID = 6;

    public const STAGE_ST3_CODE = "03";
    public const STAGE_ST4_CODE = "04";
    public const STAGE_ST5_CODE = "05";
    public const STAGE_ST6_CODE = "06";
    public const STAGE_ST7_CODE = "07";
    public const STAGE_ST8_CODE = "08";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicles_stages', 'vehicle_id', 'stage_id')->withPivot('manual', 'tracking_date')->withTimestamps();
    }
}
