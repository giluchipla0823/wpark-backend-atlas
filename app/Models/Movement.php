<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"vehicle_id", "user_id", "origin_slot_id", "destination_slot_id", "rule_id", "dt_start", "dt_end"},
 * @OA\Xml(name="Movement"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="vehicle_id", type="integer", maxLength=20, description="Indica el vehículo que se está moviendo", example="1"),
 * @OA\Property(property="user_id", type="integer", maxLength=20, description="Indica el usuario que está moviendo el vehículo", example="1"),
 * @OA\Property(property="origin_slot_id", type="integer", maxLength=20, description="Indica la posición (slot) desde donde se hace el movimiento", example="1"),
 * @OA\Property(property="destination_slot_id", type="integer", maxLength=20, description="Indica la posición (slot) haciá donde se hace el movimiento", example="2"),
 * @OA\Property(property="rule_id", type="integer", maxLength=20, description="Indica la regla que está relacionada al movimiento", example="1"),
 * @OA\Property(property="dt_start", type="string", format="date-time", description="Fecha y hora del comienzo del movimiento", example="2021-12-09 11:20:01"),
 * @OA\Property(property="dt_end", type="string", format="date-time", description="Fecha y hora del final del movimiento", example="2021-12-12 11:26:15"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Movement
 *
 */

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'origin_slot_id',
        'destination_slot_id',
        'rule_id',
        'dt_start',
        'dt_end',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function originSlot()
    {
        return $this->belongsTo(Slot::class, 'origin_slot_id');
    }

    public function destinationSlot()
    {
        return $this->belongsTo(Slot::class, 'destination_slot_id');
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }
}
