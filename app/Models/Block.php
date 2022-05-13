<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name", "is_presorting", "active"},
 * @OA\Xml(name="Block"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del bloque", example="BLOQUE ZP"),
 * @OA\Property(property="is_presorting", type="boolean", maxLength=1, description="Indica si el bloque es de presorting (0: No es presorting, 1: Es presorting)", example="1"),
 * @OA\Property(property="presorting_default", type="boolean", maxLength=1, description="Indica si el bloque de presorting es por defecto (0: No es por defecto, 1: Es por defecto)", example="1"),
 * @OA\Property(property="active", type="boolean", maxLength=1, description="Indica si el bloque está activo (0: No está activo, 1: Está activo)", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Block
 *
 */

class Block extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "is_presorting",
        "presorting_default",
        "active",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function rows()
    {
        return $this->hasMany(Row::class, 'block_id');
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'rules_blocks', 'rule_id', 'block_id')->withTimestamps();
    }
}
