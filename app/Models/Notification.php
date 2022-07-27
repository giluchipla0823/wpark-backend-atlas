<?php

namespace App\Models;

use App\Helpers\RowHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"sender_id", "type", "reference_code", "data"},
 * @OA\Xml(name="Notification"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="sender_id", type="integer", maxLength=20, description="Identificador del usuario que genera la notificación", example="1"),
 * @OA\Property(property="recipient_id", type="integer", maxLength=20, description="Indentificador del usuario al que va dirigida la notificación", example="1"),
 * @OA\Property(property="type", type="string", maxLength=255, description="Clase sobre la que se ha creado la notificación", example="Full row notification"),
 * @OA\Property(property="resourceable_type", type="string", maxLength=255, description="", example="App\Models\Row"),
 * @OA\Property(property="resourceable_id", type="integer", maxLength=20, description="", example="2"),
 * @OA\Property(property="reference_code", type="string", maxLength=255, description="Código de referencia de la notificación", example="589645V"),
 * @OA\Property(property="data", type="string", description="Datos relevantes sobre la notificación", example="{'row': '004'}"),
 * @OA\Property(property="reat_at", type="string", format="date-time", description="Fecha y hora de cuando se ha leido la notificación", example="2021-12-09 11:20:01"),
 * @OA\Property(property="deleted_at", type="string", format="date-time", description="Fecha y hora del borrado temporal", example="2021-12-09 11:20:01"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Notification
 *
 */
class Notification extends Model
{
    use HasFactory, SoftDeletes;

    public const ROW_COMPLETED = "ROW_COMPLETED";

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'type',
        'resourceable_type',
        'resourceable_id',
        'reference_code',
        'data',
        'reat_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = ["seen"];

    /**
     * @return bool
     */
    public function getSeenAttribute(): bool
    {
        return !is_null($this->reat_at);
    }


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function resourceable()
    {
        return $this->morphTo(__FUNCTION__, 'resourceable_type', 'resourceable_id');
    }

}
