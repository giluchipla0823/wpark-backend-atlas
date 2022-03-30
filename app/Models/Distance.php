<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"origin_slot_id", "destination_slot_id", "seconds"},
 * @OA\Xml(name="Distance"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="origin_slot_id", type="integer", maxLength=20, description="Indica la posición (slot) de origen", example="1"),
 * @OA\Property(property="destination_slot_id", type="integer", maxLength=20, description="Indica la posición (slot) de destino", example="2"),
 * @OA\Property(property="seconds", type="integer", maxLength=10, description="Tiempo en segundos que se tarda de la posición de origen a la de destino", example="420"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Distance
 *
 */

class Distance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'origin_slot_id',
        'destination_slot_id',
        'seconds',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function originSlot()
    {
        return $this->belongsTo(Slot::class, 'origin_slot_id');
    }

    public function destinationSlot()
    {
        return $this->belongsTo(Slot::class, 'destination_slot_id');
    }
}
