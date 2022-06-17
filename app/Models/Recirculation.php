<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class Recirculation extends Model
{
    protected $fillable = [
        "vehicle_id",
        "user_id",
        "origin_position_type",
        "origin_position_id",
        "message",
        "success",
        "back",
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->user_id = Auth::user()->id;
            $model->created_at = Carbon::now();
            $model->updated_at = Carbon::now();
        });
    }

    /**
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return MorphTo
     */
    public function originPosition(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'origin_position_type', 'origin_position_id');
    }
}
