<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"vin", "vin_shor", "design_id", "country_id", "compound_id", "eoc", "last_rule_id", "shipping_rule_id", "hybrid"},
 * @OA\Xml(name="Vehicle"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="vin", type="string", maxLength=17, description="Número de bastidor del vehículo", example="WF0FXXWPMFKY73028"),
 * @OA\Property(property="vin_short", type="string", maxLength=10, description="Últimos 7 dígitos del número de bastidor del vehículo", example="KY73028"),
 * @OA\Property(property="design_id", type="integer", maxLength=20, description="Indica el modelo del vehículo", example="1"),
 * @OA\Property(property="color_id", type="integer", maxLength=20, description="Indica el color del vehículo", example="1"),
 * @OA\Property(property="country_id", type="integer", maxLength=20, description="Indica el país de fabricación del vehículo", example="1"),
 * @OA\Property(property="destination_code_id", type="integer", maxLength=20, description="Indica el código de destino del vehículo", example="1"),
 * @OA\Property(property="slot_id", type="integer", maxLength=20, description="Indica la posición dentro de la campa del vehículo", example="1"),
 * @OA\Property(property="last_slot_id", type="integer", maxLength=20, description="Indica la última posición dentro de la campa del vehículo", example="2"),
 * @OA\Property(property="compound_id", type="integer", maxLength=20, description="Indica la campa donde está ubicado el vehículo", example="1"),
 * @OA\Property(property="eoc", type="string", maxLength=255, description="Identificador único de ford", example="5S8DQ87FZAFF090N6   WPMFKY73028  YSC B3EB  CPGD5EZJN A337C7B A6E 63  1765  MH 15"),
 * @OA\Property(property="last_rule_id", type="integer", maxLength=20, description="Indica la última regla asociada al vehículo", example="1"),
 * @OA\Property(property="shipping_rule_id", type="integer", maxLength=20, description="Indica la regla asociada al vehículo", example="1"),
 * @OA\Property(property="route_to", type="string", maxLength=100, description="", example=""),
 * @OA\Property(property="load_id", type="integer", maxLength=20, description="", example=""),
 * @OA\Property(property="hybrid", type="boolean", maxLength=1, description="Indica si el vehículo es híbrido (0: No es híbrido, 1: Es híbrido)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Vehicle
 *
 */

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    // TODO: Revisar funcionalidad de los campos y relaciones de esta tabla
    protected $fillable = [
        'vin',
        'vin_short',
        'design_id',
        'color_id',
        'country_id',
        'destination_code_id',
        'slot_id',
        'last_slot_id',
        'compound_id',
        'eoc',
        'last_rule_id',
        'shipping_rule_id',
        'route_to',
        'load_id',
        'hybrid',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function design()
    {
        return $this->belongsTo(Design::class, 'design_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function destinationCode()
    {
        return $this->belongsTo(DestinationCode::class, 'destination_code_id');
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }

    public function lastSlot()
    {
        return $this->belongsTo(Slot::class, 'last_slot_id');
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function lastRule()
    {
        return $this->belongsTo(Rule::class, 'last_rule_id');
    }

    public function shippingRule()
    {
        return $this->belongsTo(Rule::class, 'shipping_rule_id');
    }

    public function loads()
    {
        return $this->belongsTo(Load::class, 'load_id');
    }

    public function states()
    {
        return $this->belongsToMany(State::class, 'vehicles_states', 'vehicle_id', 'state_id')->wherePivotNull('deleted_at')->withTimestamps();
    }

    public function holds()
    {
        return $this->belongsToMany(Hold::class, 'holds_vehicles', 'vehicle_id', 'hold_id')->wherePivotNull('deleted_at')->withTimestamps();
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'vehicle_id');
    }
}
