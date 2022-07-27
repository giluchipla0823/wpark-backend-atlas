<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\owner\BadRequestException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public const VIN_SHORT_MAX_LENGTH = 7;

    public const CREATED_FROM_MOBILE = "mobile";
    public const CREATED_FROM_WEB = "web";

    protected $fillable = [
        'vin',
        'lvin',
        'vin_short',
        'design_id',
        'color_id',
        'destination_code_id',
        'entry_transport_id',
        'load_id',
        'route_id',
        'dealer_id',
        'eoc',
        'last_rule_id',
        'shipping_rule_id',
        'info',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['category'];

    /**
     * @param $value
     * @return string|null
     */
    public function getCategoryAttribute($value): ?string
    {
        return $this->shippingRule ? $this->shippingRule->name : null;
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class, 'design_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function destinationCode(): BelongsTo
    {
        return $this->belongsTo(DestinationCode::class, 'destination_code_id');
    }

    /**
     * @return BelongsTo
     */
    public function loads(): BelongsTo
    {
        return $this->belongsTo(Load::class, 'load_id');
    }

    /**
     * Routing code del vehículo asignado durante la carga.
     *
     * @return BelongsTo
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    /**
     * Dealer que tiene asignado el vehículo.
     *
     * @return BelongsTo
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    /**
     * Regla de presorting del vehículo.
     *
     * @return BelongsTo
     */
    public function lastRule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'last_rule_id');
    }

    /**
     * Regla de posición final de transporte del vehículo.
     *
     * @return BelongsTo
     */
    public function shippingRule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'shipping_rule_id');
    }

    /**
     * States del vehículo.
     *
     * @return BelongsToMany
     */
    public function states(): BelongsToMany
    {
        return $this->belongsToMany(State::class, 'vehicles_states', 'vehicle_id', 'state_id')->withTimestamps();
    }

    /**
     * Último State del vehículo.
     *
     * @return BelongsToMany
     */
    public function latestState(): BelongsToMany
    {
        return $this->states()->orderByDesc('id')->take(1);
    }

    /**
     * Holds que tiene asignado el vehículo.
     *
     * @return BelongsToMany
     */
    public function holds(): BelongsToMany
    {
        return $this->belongsToMany(Hold::class, 'holds_vehicles', 'vehicle_id', 'hold_id')->withTimestamps();
    }

    /**
     * Movimientos del vehículo.
     *
     * @return HasMany
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class, 'vehicle_id');
    }

    /**
     * Último movimiento (confirmado, pendiente o cancelado) del vehículo.
     *
     * @return HasOne
     */
    public function lastMovement(): HasOne
    {
        return $this->hasOne(Movement::class, 'vehicle_id')
                    ->orderBy('created_at', 'desc')
                    ->latest();
    }

    /**
     * Último movimiento confirmado del vehículo.
     *
     * @return HasOne
     */
    public function lastConfirmedMovement(): HasOne
    {
        return $this->hasOne(Movement::class, 'vehicle_id')
                    ->where('confirmed', 1)
                    ->orderBy('created_at', 'desc')
                    ->latest();
    }

    /**
     * Último movimiento pendiente del vehículo.
     *
     * @return HasOne
     */
    public function lastPendingMovement(): HasOne
    {
        return $this->hasOne(Movement::class, 'vehicle_id')
            ->where([
                ['canceled', "=", 0],
                ['confirmed', "=", 0],
            ])
            ->orderBy('created_at', 'desc')
            ->latest();
    }

    /**
     * ¿Vehículo en movimiento?
     *
     * @return bool
     */
    public function inMovement(): bool
    {
        return $this->lastMovement->confirmed === 0 &&  $this->lastMovement->canceled === 0;
    }

    /**
     * Último movimiento cancelado del vehículo.
     *
     * @return HasOne
     */
    public function lastCanceledMovement(): HasOne
    {
        return $this->hasOne(Movement::class, 'vehicle_id')
            ->where('canceled', 1)
            ->orderBy('created_at', 'desc')
            ->latest();
    }

    /**
     * Etapas del vehículo.
     *
     * @return BelongsToMany
     */
    public function stages(): BelongsToMany
    {
        return $this->belongsToMany(Stage::class, 'vehicles_stages', 'vehicle_id', 'stage_id')
                    ->withPivot('manual', 'tracking_date')
                    ->withTimestamps();
    }

    /**
     * Última etapa del vehículo.
     *
     * @return BelongsToMany
     */
    public function latestStage(): BelongsToMany
    {
        return $this->stages()->orderByDesc('id')->take(1);
    }

    /**
     * Obtener el parking del vehículo.
     *
     * @return Parking
     * @throws BadRequestException
     */
    public function getParking(): Parking {
        $destinationPosition = $this->lastConfirmedMovement->destinationPosition;

        switch (get_class($destinationPosition)) {
            case Parking::class:
                $parking = $destinationPosition;
                break;

            case Row::class:
                $parking = $destinationPosition->parking;
                break;

            case Slot::class:
                $parking = $destinationPosition->row->parking;
                break;

            default:
                throw new BadRequestException("No se pudo encontrar el parking del vehículo.");
        }

        return $parking;
    }

    /**
     * Transporte de entrada del vehículo.
     *
     * @return BelongsTo
     */
    public function transport(): BelongsTo
    {
        return $this->belongsTo(Transport::class, 'entry_transport_id');
    }

    /**
     * Recirculaciones del vehículo.
     *
     * @return HasMany
     */
    public function recirculations(): HasMany
    {
        return $this->hasMany(Recirculation::class, 'vehicle_id');
    }

    /**
     * Última recirculación del vehículo.
     *
     * @return HasOne
     */
    public function lastRecirculation(): HasOne
    {
        return $this->hasOne(Recirculation::class, 'vehicle_id')
                    ->orderBy('created_at', 'desc')
                    ->latest();
    }

}
