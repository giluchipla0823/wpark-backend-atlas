<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"wprk", "code", "rule_id", "ready", "compound_id"},
 * @OA\Xml(name="Load"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="wprk", type="string", maxLength=50, description="", example=""),
 * @OA\Property(property="oprk", type="string", maxLength=25, description="", example=""),
 * @OA\Property(property="code", type="string", maxLength=50, description="", example=""),
 * @OA\Property(property="carrier_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="rule_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="ready", type="boolean", maxLength=1, description="", example="1"),
 * @OA\Property(property="compound_id", type="integer", maxLength=20, description="", example="1"),
 * @OA\Property(property="processed", type="boolean", maxLength=1, description="", example="1"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Load
 *
 */

class Load extends Model
{
    use HasFactory, SoftDeletes;

     // TODO: Revisar funcionalidad de los campos y relaciones de esta tabla
    protected $fillable = [
        'wprk',
        'oprk',
        'code',
        'carrier_id',
        'rule_id',
        'ready',
        'compound_id',
        'processed',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Load::class, 'load_id');
    }

}
