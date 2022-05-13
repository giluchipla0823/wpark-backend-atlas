<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"vin", "vin_shor", "design_id", "color_id", "destination_code", "entry_transport_id", "eoc"},
 * @OA\Xml(name="Vehicle"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="vin", type="string", maxLength=17, description="Número de bastidor del vehículo", example="WF0FXXWPMFKY73028"),
 * @OA\Property(property="lvin", type="string", maxLength=17, description="Número de bastidor lógico del vehículo", example="WF0FXXWPMFKY73028"),
 * @OA\Property(property="vin_short", type="string", maxLength=10, description="Número de bastidor corto del vehículo", example="KY73028"),
 * @OA\Property(property="design_id", type="integer", maxLength=20, description="Indica el modelo del vehículo", example="1"),
 * @OA\Property(property="color_id", type="integer", maxLength=20, description="Indica el color del vehículo", example="1"),
 * @OA\Property(property="destination_code_id", type="integer", maxLength=20, description="Indica el código de destino del vehículo", example="1"),
 * @OA\Property(property="entry_transport_id", type="integer", maxLength=20, description="Indica el método de entrada del vehículo", example="1"),
 * @OA\Property(property="load_id", type="integer", maxLength=20, description="Indica la carga a la que pertenece el vehículo", example="1"),
 * @OA\Property(property="dealer_id", type="integer", maxLength=20, description="Indica el distribuidor al que irá el vehículo", example="2"),
 * @OA\Property(property="eoc", type="string", maxLength=255, description="Identificador único de ford", example="5S8DQ87FZAFF090N6   WPMFKY73028  YSC B3EB  CPGD5EZJN A337C7B A6E 63  1765  MH 15"),
 * @OA\Property(property="last_rule_id", type="integer", maxLength=20, description="Indica la última regla con mayor prioridad asociada al vehículo", example="1"),
 * @OA\Property(property="shipping_rule_id", type="integer", maxLength=20, description="Indica la regla de código de destino asociada al vehículo", example="1"),
 * @OA\Property(property="info", type="string", maxLength=100, description="Información adicional del vehículo", example="Pendiente de revisión"),
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

    protected $fillable = [
        'vin',
        'lvin',
        'vin_short',
        'design_id',
        'color_id',
        'destination_code_id',
        'entry_transport_id',
        'load_id',
        'dealer_id',
        'eoc',
        'last_rule_id',
        'shipping_rule_id',
        'info',
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

    public function destinationCode()
    {
        return $this->belongsTo(DestinationCode::class, 'destination_code_id');
    }

    public function loads()
    {
        return $this->belongsTo(Load::class, 'load_id');
    }

    public function dealers()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function lastRule()
    {
        return $this->belongsTo(Rule::class, 'last_rule_id');
    }

    public function shippingRule()
    {
        return $this->belongsTo(Rule::class, 'shipping_rule_id');
    }

    public function states()
    {
        return $this->belongsToMany(State::class, 'vehicles_states', 'vehicle_id', 'state_id')->withTimestamps();
    }

    public function latestState()
    {
        return $this->belongsToMany(
            State::class,
            'vehicles_states',
            'vehicle_id',
            'state_id'
        )
        ->orderByPivot('created_at', 'desc')
        ->take(1);
    }

    public function holds()
    {
        return $this->belongsToMany(Hold::class, 'holds_vehicles', 'vehicle_id', 'hold_id')->withTimestamps();
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'vehicle_id');
    }

    public function lastMovement()
    {
        return $this->hasOne(Movement::class, 'vehicle_id')->orderBy('created_at', 'desc')->latest();
    }

    public function stages()
    {
        return $this->belongsToMany(Stage::class, 'vehicles_stages', 'vehicle_id', 'stage_id')->withPivot('manual', 'tracking_date')->withTimestamps();
    }

    public function latestStage()
    {
        return $this->belongsToMany(
            Stage::class,
            'vehicles_stages',
            'vehicle_id',
            'stage_id'
        )
        ->withPivot('manual', 'tracking_date')
        ->withTimestamps()
        ->orderByPivot('created_at', 'desc')
        ->take(1);
    }

    /* public function getStage($stage)
    {
        return $this->belongsToMany(Stage::class, 'vehicles_stages', 'vehicle_id', 'stage_id', 'manual', 'trackind_date')->withTimestamps()->where('stage_id', $stage);
    } */

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

}
