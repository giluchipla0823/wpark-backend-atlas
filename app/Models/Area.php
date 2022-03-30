<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "compound_id", "zone_id", "rows", "columns", "capacity"},
 * @OA\Xml(name="Area"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del área", example="AREA 1"),
 * @OA\Property(property="compound_id", type="integer", maxLength=20, description="Indica la campa a la que pertence el área", example="1"),
 * @OA\Property(property="zone_id", type="integer", maxLength=20, description="Indica la zona a la que pertence el área", example="1"),
 * @OA\Property(property="rows", type="integer", maxLength=10, description="Número de filas que tiene el área", example="30"),
 * @OA\Property(property="capacity", type="integer", maxLength=10, description="Capacidad (total de slots) del área", example="300"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Area
 *
 */

class Area extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'compound_id',
        'zone_id',
        'rows',
        'capacity',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function parkings()
    {
        return $this->hasMany(Parking::class, 'area_id');
    }

}
