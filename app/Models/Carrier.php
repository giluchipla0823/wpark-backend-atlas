<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "short_name", "code", "is_train"},
 * @OA\Xml(name="Carrier"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la empresa de transporte", example="SINTAX LOGISTICA SA"),
 * @OA\Property(property="short_name", type="string", maxLength=255, description="Nombre corto de la empresa de transporte", example="SINTAX"),
 * @OA\Property(property="code", type="string", maxLength=25, description="Código de la empresa de transporte", example="BKT9A"),
 * @OA\Property(property="is_train", type="boolean", maxLength=1, description="Indica si el medio de transporte es en tren (0: No es en tren, 1: Es en tren)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Carrier
 *
 */

class Carrier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'short_name',
        'code',
        'is_train',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function routes()
    {
        return $this->hasMany(Route::class, 'carrier_id');
    }

    public function rules()
    {
        return $this->hasMany(Rule::class, 'carrier_id');
    }

    public function loads()
    {
        return $this->hasMany(Load::class, 'carrier_id');
    }

}
