<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'compound_id',
        'device_id'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }
}
