<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "area_id", "parking_type_id", "active"},
 * @OA\Xml(name="Parking"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del parking", example="PU"),
 * @OA\Property(property="area_id", type="integer", maxLength=20, description="Indica el área al que pertenece el parking", example="1"),
 * @OA\Property(property="parking_type_id", type="integer", maxLength=20, description="Indica el tipo de parking", example="1"),
 * @OA\Property(property="start_row", type="integer", maxLength=10, description="La fila del área en la que empieza el parking", example="12"),
 * @OA\Property(property="end_row", type="integer", maxLength=10, description="La fila del área en la que termina el parking", example="22"),
 * @OA\Property(property="capacity", type="integer", maxLength=10, description="Capacidad (número de slots) del parking", example="80"),
 * @OA\Property(property="fill", type="integer", maxLength=10, description="Número de slots ocupados en el parking", example="400.000"),
 * @OA\Property(property="full", type="boolean", maxLength=1, description="Indica si el parking está lleno (0: No está lleno, 1: Está lleno)", example="0"),
 * @OA\Property(property="order", type="boolean", maxLength=1, description="Indica si se comienza a llenar desde la primera fila o la última (0: Orden Descendente, 1: Orden Ascendente)", example="1"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el parking está activo (0: No está activo, 1: Está activo)", example="1"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre el parking", example="Este parking deberá cambiar de ubicación"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Parking
 *
 */

class Parking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'area_id',
        'parking_type_id',
        'start_row',
        'end_row',
        'capacity',
        'fill',
        'full',
        'order',
        'active',
        'comments',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function parkingType()
    {
        return $this->belongsTo(ParkingType::class, 'parking_type_id');
    }

    public function rows()
    {
        return $this->hasMany(Row::class, 'parking_id');
    }

    public function rulesPredefinedParking()
    {
        return $this->hasMany(Rule::class, 'predefined_zone_id');
    }
}
