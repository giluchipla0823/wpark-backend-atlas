<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "code", "zip_code", "city", "street", "country"},
 * @OA\Xml(name="Dealer"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del distribuidor", example="MOTOREBRE  S.A."),
 * @OA\Property(property="code", type="string", maxLength=255, description="Código del distribuidor", example="1045V"),
 * @OA\Property(property="zip_code", type="string", maxLength=255, description="Código postal del distribuidor", example="43870"),
 * @OA\Property(property="city", type="string", maxLength=255, description="Ciudad del distribuidor", example="AMPOSTA"),
 * @OA\Property(property="street", type="string", maxLength=255, description="Dirección del distribuidor", example="AVINGUDA DE SANT JAUME, S/N"),
 * @OA\Property(property="country", type="string", maxLength=255, description="Pais del distribuidor", example="España"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Dealer
 *
 */

class Dealer extends Model
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
        'zip_code',
        'city',
        'street',
        'country',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'dealer_id');
    }

}
