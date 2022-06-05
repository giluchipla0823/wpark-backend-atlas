<?php

namespace App\Models;

use App\Helpers\AppHelper;
use App\Helpers\RowHelper;
use App\Http\Resources\Row\RowResource;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"row_number", "parking_id", "capacity", "capacitymm", "alt_qr"},
 * @OA\Xml(name="Row"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="row_number", type="integer", maxLength=10, description="Número de fila del parking", example="1"),
 * @OA\Property(property="parking_id", type="integer", maxLength=20, description="Indica el parking al que pertenece la fila", example="1"),
 * @OA\Property(property="block_id", type="integer", maxLength=20, description="Indica el bloque al que pertenece la fila", example="1"),
 * @OA\Property(property="rule_id", type="integer", maxLength=20, description="Indica la regla de posición final de transporte heredada del primer vehículo que se posicionó en la fila", example="1"),
 * @OA\Property(property="capacity", type="integer", maxLength=10, description="Número de slots que tiene la fila", example="8"),
 * @OA\Property(property="fill", type="integer", maxLength=10, description="Número de slots ocupados en la fila", example="0"),
 * @OA\Property(property="capacitymm", type="integer", maxLength=10, description="Capacidad en milímetros de la fila", example="40.000"),
 * @OA\Property(property="fillmm", type="integer", maxLength=10, description="Capacidad en milímetros ocupados de la fila", example="0"),
 * @OA\Property(property="full", type="boolean", maxLength=1, description="Indica si la fila está llena (0: No está llena, 1: Está llena)", example="0"),
 * @OA\Property(property="alt_qr", type="string", description="Código QR de la fila", example="022.001"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre la fila", example="La fila está reservada"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si la fila está activa (0: No está activa, 1: Está activa)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Row
 *
 */
class Row extends Model
{
    use HasFactory, SoftDeletes;

    // Cantidad de slots máximo que puede tener una fila de espiga
    public const ESPIGA_CAPACITY = 1;

    // Resource
    public $notificationResource = RowResource::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'row_number',
        'parking_id',
        'block_id',
        'rule_id',
        'capacity',
        'fill',
        'capacitymm',
        'fillmm',
        'full',
        'alt_qr',
        'comments',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $appends = ["row_name", "category", "fill_percentage", "fill_type", "lp_name", "lp_code"];

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }

    public function slots()
    {
        return $this->hasMany(Slot::class, 'row_id');
    }

    public function emptySlots()
    {
        return $this->hasMany(Slot::class, 'row_id')->where('fill', 0);
    }

    public function rulesOverflowRows()
    {
        return $this->hasMany(Rule::class, 'overflow_id');
    }

    /**
     * @return string
     */
    public function getRowNameAttribute(): string
    {
        return $this->parking->name .  "." . RowHelper::zeroFill($this->row_number);
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

        return round(($this->fill / $capacity) * 100, 2);
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
    public function getCategoryAttribute(): ?string
    {
        return $this->rule ? $this->rule->name : null;
    }

    /**
     * @return string|null
     */
    public function getLpNameAttribute(): ?string
    {
        $parking = $this->parking;
        $rowNumber = ltrim($this->row_number, "0");

        return "{$parking->area->compound->name}.{$parking->name}.{$rowNumber}.0";
    }

    /**
     * @return string|null
     */
    public function getLpCodeAttribute(): ?string
    {
        $parking = $this->parking;

        return "{$parking->area->compound->id}.{$parking->id}.{$this->id}.0";
    }

    /**
     * @return Attribute
     */
    protected function rowNumber(): Attribute
    {
        return new Attribute(
            function ($value) {
                return RowHelper::zeroFill($value);
            }
        );
    }
}
