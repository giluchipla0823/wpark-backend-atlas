<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "model_state_id"},
 * @OA\Xml(name="State"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del estado", example="STATIONED"),
 * @OA\Property(property="description", type="string", maxLength=255, description="Descripción del estado", example="El vehículo está estacionado"),
 * @OA\Property(property="model_state_id", type="integer", maxLength=20, description="Indica si el estado será para un vehículo o para una fila", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class State
 *
 */

class State extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'model_state_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function modelState()
    {
        return $this->belongsTo(ModelState::class, 'model_state_id');
    }

    public function rows()
    {
        return $this->belongsToMany(Row::class, 'rows_states', 'row_id', 'state_id')->wherePivotNull('deleted_at')->withTimestamps();
    }

    public function rules()
    {
        return $this->hasMany(Rule::class, 'next_state_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicles_states', 'vehicle_id', 'state_id')->wherePivotNull('deleted_at')->withTimestamps();
    }
}
