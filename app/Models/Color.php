<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "code", "simple_name"},
 * @OA\Xml(name="Color"),
 * @OA\Property(property="id", type="integer", maxLength=10, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del color", example="RUBY RED"),
 * @OA\Property(property="code", type="string", maxLength=255, description="Código de color único", example="RYBB"),
 * @OA\Property(property="simple_name", type="string", maxLength=255, description="Datetime marker of verification status", example="RED"),
 * @OA\Property(property="hex", type="string", maxLength=255, example="#9b111e"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Color
 *
 */

class Color extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'simple_name',
        'hex',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'color_id');
    }

}
