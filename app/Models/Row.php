<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"row_number", "parking_id", "capacity", "capacitymm", "alt_qr"},
 * @OA\Xml(name="Row"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="row_number", type="integer", maxLength=10, description="Número de fila del parking", example="1"),
 * @OA\Property(property="parking_id", type="integer", maxLength=20, description="Indica el parking al que pertenece la fila", example="1"),
 * @OA\Property(property="block_id", type="integer", maxLength=20, description="Indica el bloque al que pertenece la fila", example="1"),
 * @OA\Property(property="capacity", type="integer", maxLength=10, description="Número de slots que tiene la fila", example="8"),
 * @OA\Property(property="fill", type="integer", maxLength=10, description="Número de slots ocupados en la fila", example="0"),
 * @OA\Property(property="capacitymm", type="integer", maxLength=10, description="Capacidad en milímetros de la fila", example="40.000"),
 * @OA\Property(property="fillmm", type="integer", maxLength=10, description="Capacidad en milímetros ocupados de la fila", example="0"),
 * @OA\Property(property="alt_qr", type="string", description="Código QR de la fila", example="022.001"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre la fila", example="La fila está reservada"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Row
 *
 */

class Row extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'row_number',
        'parking_id',
        'block_id',
        'capacity',
        'fill',
        'capacitymm',
        'fillmm',
        'alt_qr',
        'comments',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function states()
    {
        return $this->belongsToMany(State::class, 'rows_states', 'row_id', 'state_id')->withTimestamps();
    }

    public function slots()
    {
        return $this->hasMany(Slot::class, 'row_id');
    }

    public function rulesPredefinedRows()
    {
        return $this->hasMany(Rule::class, 'predefined_zone_id');
    }

    public function rulesOverflowRows()
    {
        return $this->hasMany(Rule::class, 'overflow_id');
    }
}
