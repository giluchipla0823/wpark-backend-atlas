<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "code", "country_id"},
 * @OA\Xml(name="DestinationCode"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=100, description="Nombre del código de destino", example="ANTWERP_CHINA"),
 * @OA\Property(property="code", type="string", maxLength=5, description="Código del código de destino", example="AC"),
 * @OA\Property(property="country_id", type="integer", maxLength=20, description="Indica el país del código de destino", example="1"),
 * @OA\Property(property="description", type="string", maxLength=255, description="Descripción del código de destino", example="Código de destino para Italia"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el código de destino está activo (0: No está activo, 1: Está activo)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class DestinationCode
 *
 */

class DestinationCode extends Model
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
        'country_id',
        'description',
        'active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'destination_code_id');
    }

    public function routes()
    {
        return $this->hasMany(Route::class, 'dealer_id');
    }

}
