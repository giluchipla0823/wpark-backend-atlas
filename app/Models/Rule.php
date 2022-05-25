<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "is_group", "active"},
 * @OA\Xml(name="Rule"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la regla", example="FORD"),
 * @OA\Property(property="countdown", type="integer", maxLength=10, description="Número de vehículos máximo para aplicarles la regla", example="1"),
 * @OA\Property(property="priority", type="integer", maxLength=1, description="Indica el orden de prioridad de la regla", example="1"),
 * @OA\Property(property="is_group", type="boolean", maxLength=1, description="Indica si es una regla simple o un grupo de reglas (0: Regla simple, 1: Grupo de reglas)", example="0"),
 * @OA\Property(property="final_position", type="boolean", maxLength=1, description="Indica si la regla es de posición final (0: No es posición final, 1: Es posición final)", example="1"),
 * @OA\Property(property="predefined_zone_id", type="integer", maxLength=20, description="Indica el parking al que va asociada la regla", example="1"),
 * @OA\Property(property="carrier_id", type="integer", maxLength=20, description="Indica el transportista por defecto asociado a la regla", example="1"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si la regla está activa (0: No está activa, 1: Está activa)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Rule
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

    protected $fillable = [
        'name',
        'countdown',
        'priority',
        'is_group',
        'final_position',
        'predefined_zone_id',
        'carrier_id',
        'active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function predefinedParking()
    {
        return $this->belongsTo(Parking::class, 'predefined_zone_id');
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

    public function vehiclesLastRules()
    {
        return $this->hasMany(Vehicle::class, 'last_rule_id');
    }

    public function vehiclesShippingRules()
    {
        return $this->hasMany(Vehicle::class, 'shipping_rule_id');
    }

    public function rules_groups()
    {
        return $this->belongsToMany(Rule::class, 'rules_groups', 'parent_rule_id', 'child_rule_id')->withTimestamps();
    }

    public function rows()
    {
        return $this->hasMany(Row::class, 'rule_id');
    }

}
