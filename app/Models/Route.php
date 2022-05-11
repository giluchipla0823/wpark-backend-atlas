<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "cdm_code", "route_type_id", "carrier_id", "destination_code_id", "origin_compund_id"},
 * @OA\Xml(name="Route"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la ruta", example="ANTWERP CHINA"),
 * @OA\Property(property="cdm_code", type="string", maxLength=5, description="Código de la ruta", example="ANTC1"),
 * @OA\Property(property="route_type_id", type="integer", maxLength=20, description="Indica el tipo de ruta", example="1"),
 * @OA\Property(property="carrier_id", type="integer", maxLength=20, description="Indica la empresa de transporte que hace la ruta", example="1"),
 * @OA\Property(property="destination_code_id", type="integer", maxLength=20, description="Indica el código de destino de la ruta", example="1"),
 * @OA\Property(property="origin_compound_id", type="integer", maxLength=20, description="Indica la campa de origen", example="1"),
 * @OA\Property(property="destination_compound_id", type="integer", maxLength=20, description="Indica la campa de destino", example="2"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre la ruta", example="Esta ruta actualmente tiene un desvío"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Route
 *
 */

class Route extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cdm_code',
        'route_type_id',
        'carrier_id',
        'destination_code_id',
        'origin_compound_id',
        'destination_compound_id',
        'comments',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function route_type()
    {
        return $this->belongsTo(RouteType::class, 'route_type_id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    public function destinationCode()
    {
        return $this->belongsTo(DestinationCode::class, 'destination_code_id');
    }

    public function originCompound()
    {
        return $this->belongsTo(Compound::class, 'origin_compound_id');
    }

    public function destinationCompound()
    {
        return $this->belongsTo(Compound::class, 'destination_compound_id');
    }

}
