<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\Parking\ParkingResource;

/**
 *
 * @OA\Schema(
 * required={"name", "area_id", "parking_type_id", "active"},
 * @OA\Xml(name="Parking"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del parking", example="PU"),
 * @OA\Property(property="area_id", type="integer", maxLength=20, description="Indica el área al que pertenece el parking", example="1"),
 * @OA\Property(property="parking_type_id", type="integer", maxLength=20, description="Indica el tipo de parking", example="1"),
 * @OA\Property(property="start_row", type="integer", maxLength=10, description="La fila del área en la que empieza el parking", example="12"),
 * @OA\Property(property="end_row", type="integer", maxLength=10, description="La fila del área en la que termina el parking", example="22"),
 * @OA\Property(property="capacity", type="integer", maxLength=10, description="Capacidad (número de slots) del parking", example="80"),
 * @OA\Property(property="fill", type="integer", maxLength=10, description="Número de slots ocupados en el parking", example="400.000"),
 * @OA\Property(property="full", type="boolean", maxLength=1, description="Indica si el parking está lleno (0: No está lleno, 1: Está lleno)", example="0"),
 * @OA\Property(property="order", type="boolean", maxLength=1, description="Indica si se comienza a llenar desde la primera fila o la última (0: Orden Descendente, 1: Orden Ascendente)", example="1"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el parking está activo (0: No está activo, 1: Está activo)", example="1"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre el parking", example="Este parking deberá cambiar de ubicación"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Parking
 *
 */

class Parking extends Model
{
    use HasFactory, SoftDeletes;

    // Resource
    public $movementResource = ParkingResource::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'area_id',
        'parking_type_id',
        'start_row',
        'end_row',
        'capacity',
        'fill',
        'full',
        'order',
        'active',
        'comments',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = ["fill_percentage", "fill_type", "fill_calculate", "lp_name", "lp_code"];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function parkingType()
    {
        return $this->belongsTo(ParkingType::class, 'parking_type_id');
    }

    public function rows()
    {
        return $this->hasMany(Row::class, 'parking_id');
    }

    public function rulesPredefinedParking()
    {
        return $this->hasMany(Rule::class, 'predefined_zone_id');
    }

    public function originMovement()
    {
        return $this->morphMany(Movement::class, 'originPosition');
    }

    public function destinationMovement()
    {
        return $this->morphMany(Movement::class, 'destinationPosition');
    }

    /**
     * @return int
     */
    public function getFillCalculateAttribute(): int
    {
        return $this->rows->sum("real_fill");
    }

    /**
     * @return float
     */
    public function getFillPercentageAttribute(): float
    {
        $capacity = $this->capacity ?: 0;

        if ($capacity === 0) {
            return $capacity;
        }

        return round(($this->fill_calculate / $capacity) * 100, 2);
    }

    /**
     * @return string
     */
    public function getFillTypeAttribute(): string
    {
        return AppHelper::getFillTypeToParkingOrRow($this->fill_percentage);
    }

    /**
     * @return string|null
     */
    public function getLpNameAttribute(): ?string
    {
        return "{$this->area->compound->name}.{$this->name}.0.0";
    }

    /**
     * @return string|null
     */
    public function getLpCodeAttribute(): ?string
    {
        return "{$this->area->compound->id}.{$this->id}.0.0";
    }

    /**
     * Si el parking es de tipo "FILAS"
     * @return bool
     */
    public function isRowType(): bool
    {
        return $this->parking_type_id === ParkingType::TYPE_ROW;
    }

    /**
     * Si el parking es de tipo "ESPIGA"
     * @return bool
     */
    public function isEspigaType(): bool
    {
        return $this->parking_type_id === ParkingType::TYPE_ESPIGA;
    }

    /**
     * Si el parking es de tipo "UNLIMITED"
     * @return bool
     */
    public function isUnlimitedType(): bool
    {
        return $this->parking_type_id === ParkingType::TYPE_UNLIMITED;
    }

    /**
     * @return void
     */
    public function reserve(): void
    {
        $this->increment("fill");
    }

    /**
     * @return void
     */
    public function release(): void
    {
        $this->decrement("fill");
    }
}
