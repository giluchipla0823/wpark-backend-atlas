<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"license_plate", "transport_identifier", "ready", "compound_id"},
 * @OA\Xml(name="Load"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="transport_identifier", type="string", maxLength=50, description="Código de la carga/albarán", example="4529465"),
 * @OA\Property(property="license_plate", type="string", maxLength=50, description="Matrícula principal del método de transporte", example="45852-FRL"),
 * @OA\Property(property="trailer_license_plate", type="string", maxLength=25, description="Matrícula del remolque del método de transporte", example="58463-JKI"),
 * @OA\Property(property="carrier_id", type="integer", maxLength=20, description="Indica la empresa de transporte que realiza la carga", example="1"),
 * @OA\Property(property="exit_transport_id", type="integer", maxLength=20, description="Indica el medio de transporte de salida de los vehículos", example="1"),
 * @OA\Property(property="compound_id", type="integer", maxLength=20, description="Indica la campa donde se realiza la carga", example="1"),
 * @OA\Property(property="ready", type="boolean", maxLength=1, description="Indica si la carga está preprada (0: No está preparada, 1: Está preparada)", example="1"),
 * @OA\Property(property="processed", type="boolean", maxLength=1, description="Indica si la carga ya se ha realizado (0: No se ha realizado, 1: Se ha realizado)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Load
 *
 */

class Load extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transport_identifier',
        'license_plate',
        'trailer_license_plate',
        'carrier_id',
        'exit_transport_id',
        'compound_id',
        'ready',
        'processed',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'exit_transport_id');
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'load_id');
    }

}
