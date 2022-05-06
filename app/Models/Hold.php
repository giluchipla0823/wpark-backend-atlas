<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "code", "priority", "active"},
 * @OA\Xml(name="Hold"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del bloqueo", example="STOLEN"),
 * @OA\Property(property="code", type="string", maxLength=25, description="Código del bloqueo", example="AZ"),
 * @OA\Property(property="priority", type="integer", maxLength=10, description="Indica el orden de prioridad del bloqueo", example="1"),
 * @OA\Property(property="role_id", type="integer", maxLength=20, description="Indica el rol del usuario que puede hacer uso del bloqueo", example="1"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el bloqueo está activo (0: No está activo, 1: Está activo)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Hold
 *
 */

class Hold extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'priority',
        'role_id',
        'active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function conditions()
    {
        return $this->belongsToMany(Condition::class, 'holds_conditions', 'hold_id', 'condition_id')->withTimestamps();
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'holds_vehicles', 'vehicle_id', 'hold_id')->withTimestamps();
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
