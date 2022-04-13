<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name"},
 * @OA\Xml(name="Condition"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la condición", example="CÓDIGO DESTINO"),
 * @OA\Property(property="description", type="string", maxLength=255, description="Descripción de la condición", example="Condición por códigos de destino"),
 * @OA\Property(property="model_condition_id", type="integer", maxLength=20, description="Indica si la condición será para un hold o para una regla", example="1"),
 * @OA\Property(property="required", type="boolean", maxLength=1, description="Indica si la condición es obligatoria (0: No es obligatoria, 1: Es obligatoria)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Condition
 *
 */

class Condition extends Model
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
        'model_condition_id',
        'required',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function modelCondition()
    {
        return $this->belongsTo(ModelCondition::class, 'model_condition_id');
    }

    public function holds()
    {
        return $this->belongsToMany(Hold::class, 'holds_conditions', 'hold_id', 'condition_id')->withTimestamps();
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'rules_conditions', 'rule_id', 'condition_id')->withPivot('conditionable_type', 'conditionable_id')->withTimestamps();
    }

}
