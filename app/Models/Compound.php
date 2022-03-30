<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name"},
 * @OA\Xml(name="Compound"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la campa", example="Valencia Ford Plant"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Compound
 *
 */

class Compound extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_compounds', 'user_id', 'compound_id')->wherePivotNull('deleted_at')->withTimestamps();
    }

    public function routesOriginCompounds()
    {
        return $this->hasMany(Route::class, 'origin_compound_id');
    }

    public function routesDestinationCompounds()
    {
        return $this->hasMany(Route::class, 'destination_compound_id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'compound_id');
    }

    public function brands()
    {
        return $this->hasMany(Brand::class, 'compound_id');
    }

    public function rules()
    {
        return $this->hasMany(Rule::class, 'compound_id');
    }

    public function loads()
    {
        return $this->hasMany(Load::class, 'compound_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'compound_id');
    }

}
