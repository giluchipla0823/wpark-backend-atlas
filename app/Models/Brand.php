<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "code"},
 * @OA\Xml(name="Brand"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la marca", example="FORD"),
 * @OA\Property(property="code", type="string", maxLength=255, description="Código de la marca", example="12"),
 * @OA\Property(property="compound_id", type="integer", maxLength=20, description="Indica la campa asociada a la marca", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Brand
 *
 */

class Brand extends Model
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
        'compound_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function designs()
    {
        return $this->hasMany(Design::class, 'brand_id');
    }

}
