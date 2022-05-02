<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "code", "brand_id", "length", "width", "height", "weight", "description", "manufacturing", "hybrid", "svg"},
 * @OA\Xml(name="Design"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del modelo", example="KUGA 2021"),
 * @OA\Property(property="short_name", type="string", maxLength=255, description="Nombre corto del modelo", example="KUGA"),
 * @OA\Property(property="code", type="string", maxLength=25, description="Código del modelo", example="C250"),
 * @OA\Property(property="brand_id", type="integer", maxLength=20, description="Indica la marca a la que pertence el modelo", example="1"),
 * @OA\Property(property="length", type="integer", maxLength=10, description="Longitud del modelo", example="4614"),
 * @OA\Property(property="width", type="integer", maxLength=10, description="Anchura del modelo", example="1882"),
 * @OA\Property(property="height", type="integer", maxLength=10, description="Altura del modelo", example="1661"),
 * @OA\Property(property="weight", type="integer", maxLength=10, description="Peso del modelo", example="1580"),
 * @OA\Property(property="description", type="string", maxLength=255, description="Descripción del modelo", example="Kuga new"),
 * @OA\Property(property="hybrid", type="boolean", maxLength=1, description="Indica si el vehículo es híbrido (0: No es híbrido, 1: Es híbrido)", example="1"),
 * @OA\Property(property="manufacturing", type="boolean", maxLength=1, description="Indica si el modelo está fabricado (0: No fabricado, 1: Fabricado)", example="1"),
 * @OA\Property(property="svg", type="string", description="Imagen del modelo", example="<svg>...</svg>"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Design
 *
 */

class Design extends Model
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
        'brand_id',
        'length',
        'width',
        'height',
        'weight',
        'description',
        'hybrid',
        'manufacturing',
        'svg',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'design_id');
    }

}
