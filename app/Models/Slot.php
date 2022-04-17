<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"slot_number", "row_id", "capacity", "fill", "capacitymm", "fillmm"},
 * @OA\Xml(name="Slot"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="slot_number", type="integer", maxLength=10, description="Número de slot de la fila", example="1"),
 * @OA\Property(property="row_id", type="integer", maxLength=20, description="Indica la fila a la que pertenece el slot", example="1"),
 * @OA\Property(property="capacity", type="integer", maxLength=10, description="Capacidad del slot", example="1"),
 * @OA\Property(property="fill", type="integer", maxLength=10, description="Indica si el slot está ocupado", example="1"),
 * @OA\Property(property="capacitymm", type="integer", maxLength=10, description="Capacidad en milímetros del slot", example="5.000"),
 * @OA\Property(property="fillmm", type="integer", maxLength=10, description="Capacidad en milímetros ocupados del slot", example="4.612"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre el slot", example="La slot está reservado"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Slot
 *
 */

class Slot extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slot_number',
        'row_id',
        'capacity',
        'fill',
        'capacitymm',
        'fillmm',
        'comments',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function row()
    {
        return $this->belongsTo(Row::class, 'row_id');
    }

    public function distancesOriginSlots()
    {
        return $this->hasMany(Distance::class, 'origin_slot_id');
    }

    public function distancesDestinationSlots()
    {
        return $this->hasMany(Distance::class, 'destination_slot_id');
    }

    public function vehiclesSlot()
    {
        return $this->hasMany(Vehicle::class, 'slot_id');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'slot_id');
    }

    public function vehiclesLastSlot()
    {
        return $this->hasMany(Vehicle::class, 'last_slot_id');
    }

    public function movementsOriginSlots()
    {
        return $this->hasMany(Movement::class, 'origin_slot_id');
    }

    public function movementsDestinationSlots()
    {
        return $this->hasMany(Movement::class, 'destination_slot_id');
    }

}
