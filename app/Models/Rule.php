<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "active"},
 * @OA\Xml(name="Row"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la regla", example="FORD"),
 * @OA\Property(property="countdown", type="integer", maxLength=10, description="", example="1"),
 * @OA\Property(property="priority", type="boolean", maxLength=1, description="Indica el orden de prioridad de la regla", example="1"),
 * @OA\Property(property="predefined_zone_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="overflow_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="next_state_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="compound_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="carrier_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el bloqueo está activo (0: No está activo, 1: Está activo)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Row
 *
 */

class Rule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // TODO: Revisar funcionalidad de los campos y relaciones de esta tabla
    protected $fillable = [
        'name',
        'countdown',
        'priority',
        'predefined_zone_id',
        'overflow_id',
        'next_state_id',
        'compound_id',
        'carrier_id',
        'active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function predefinedRow()
    {
        return $this->belongsTo(Row::class, 'predefined_zone_id');
    }

    public function overflowRow()
    {
        return $this->belongsTo(Row::class, 'overflow_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    public function blocks()
    {
        return $this->belongsToMany(Block::class, 'rules_blocks', 'rule_id', 'block_id')->withTimestamps();
    }

    public function conditions()
    {
        return $this->belongsToMany(Condition::class, 'rules_conditions', 'rule_id', 'condition_id')->withPivot('conditionable_type', 'conditionable_id')->withTimestamps();
    }

    public function loads()
    {
        return $this->hasMany(Load::class, 'rule_id');
    }

    public function vehiclesLastRules()
    {
        return $this->hasMany(Vehicle::class, 'last_rule_id');
    }

    public function vehiclesShippingRules()
    {
        return $this->hasMany(Vehicle::class, 'shipping_rule_id');
    }

    public function movements()
    {
        return $this->hasMany(Rule::class, 'rule_id');
    }

}
