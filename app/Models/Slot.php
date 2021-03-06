<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\Slot\SlotResource;
use App\Helpers\RowHelper;

/**
 *
 * @OA\Schema(
 * required={"slot_number", "row_id", "capacity", "fill", "capacitymm", "fillmm"},
 * @OA\Xml(name="Slot"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="slot_number", type="integer", maxLength=10, description="Número de slot de la fila", example="1"),
 * @OA\Property(property="row_id", type="integer", maxLength=20, description="Indica la fila a la que pertenece el slot", example="1"),
 * @OA\Property(property="capacity", type="integer", maxLength=10, description="Capacidad del slot", example="1"),
 * @OA\Property(property="fill", type="integer", maxLength=10, description="Indica si el slot está ocupado", example="1"),
 * @OA\Property(property="capacitymm", type="integer", maxLength=10, description="Capacidad en milímetros del slot", example="5.000"),
 * @OA\Property(property="fillmm", type="integer", maxLength=10, description="Capacidad en milímetros ocupados del slot", example="4.612"),
 * @OA\Property(property="comments", type="string", description="Comentarios sobre el slot", example="La slot está reservado"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Slot
 *
 */

class Slot extends Model
{
    use HasFactory, SoftDeletes;

    // Capacidad máxima por slot (en mm)
    public const CAPACITY_MM = 4800;

    // Resource
    public $movementResource = SlotResource::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "slot_number",
        "row_id",
        "capacity",
        "fill",
        "capacitymm",
        "fillmm",
        "comments",
        "deleted_at",
        "created_at",
        "updated_at",
    ];

    protected $appends = ["row_name", "lp_name", "lp_code", "real_fill"];

    /**
     * @param $value
     * @return string
     */
    public function getRowNameAttribute($value): string
    {
        return $this->row->parking->name .  "." . RowHelper::zeroFill($this->row->row_number);
    }

    public function row()
    {
        return $this->belongsTo(Row::class, "row_id");
    }

    public function distancesOriginSlots()
    {
        return $this->hasMany(Distance::class, "origin_slot_id");
    }

    public function distancesDestinationSlots()
    {
        return $this->hasMany(Distance::class, "destination_slot_id");
    }

    public function originMovement()
    {
        return $this->morphMany(Movement::class, "originPosition");
    }

    public function destinationMovement()
    {
        return $this->morphOne(Movement::class, Slot::class, 'destination_position_type', "destination_position_id")->orderBy('created_at', 'desc');
    }

    public function lastDestinationMovement()
    {
        return $this->hasOne(Movement::class, 'destination_position_id')
                    ->where('destination_position_type', Slot::class)
                    ->orderBy('created_at', 'desc')->latest();
    }

    public function lastConfirmedDestinationMovement()
    {
        return $this->hasOne(Movement::class, 'destination_position_id')
            ->where('destination_position_type', Slot::class)
            ->where('confirmed', 1)
            ->orderBy('created_at', 'desc')->latest();
    }

    public function next() {
        return $this->query()
                ->where('id', '>', $this->id)
                ->where('row_id', '=', $this->row_id)
                ->orderBy('id', 'asc')
                ->first();
    }

    public function previous() {
        // return User::where('id', '<', $this->id)->orderBy('id','desc')->first();
        return $this->query()
                    ->where('id', '<', $this->id)
                    ->where('row_id', '=', $this->row_id)
                    ->orderBy('id','desc')
                    ->first();
    }

    /**
     * @return string|null
     */
    public function getLpNameAttribute(): ?string
    {
        $row = $this->row;
        $parking = $row->parking;
        $rowNumber = ltrim($row->row_number, "0");

        return "{$parking->area->compound->name}.{$parking->name}.{$rowNumber}.{$this->slot_number}";
    }

    /**
     * @return string|null
     */
    public function getLpCodeAttribute(): ?string
    {
        $row = $this->row;
        $parking = $row->parking;

        return "{$parking->area->compound->id}.{$parking->id}.{$row->id}.{$this->id}";
    }

    /**
     * @return int
     */
    public function getRealFillAttribute(): int
    {
        return (int) (
            $this->fill > 0 &&
            $this->lastDestinationMovement &&
            $this->lastDestinationMovement->confirmed === 1
        );
    }

    /**
     * Reservar slot.
     *
     * @param int $vehicleLength
     * @return void
     */
    public function reserve(int $vehicleLength): void
    {
        $this->increment("fill");
        $this->increment("fillmm", $vehicleLength);

        $row = $this->row;
        $row->increment("fill");
        $row->increment("fillmm", $vehicleLength);
        $row->parking->increment("fill");
    }

    /**
     * @param int $vehicleLength
     * @return void
     */
    public function release(int $vehicleLength): void
    {
        if ($this->fill > 1) {
            $this->decrement("fill");
            $this->decrement("fillmm", $vehicleLength);
        } else {
            $this->fill = 0;
            $this->fillmm = 0;
            $this->save();
        }

        $row = $this->row;
        $row->decrement("fill");
        $row->decrement("fillmm", $vehicleLength);

        $mustSaveRow = false;

        if ($row->fill === 0) {
            $row->category = null;
            $mustSaveRow = true;
        }

        if (($row->capacitymm - $row->fillmm) >= Slot::CAPACITY_MM) {
            $row->full = 0;
            $mustSaveRow = true;
        }

        if ($mustSaveRow) {
            $row->save();
        }

        $row->parking->decrement("fill");
    }

}
