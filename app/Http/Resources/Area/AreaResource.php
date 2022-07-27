<?php

namespace App\Http\Resources\Area;

use App\Http\Resources\Zone\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Compound\CompoundResource;

class AreaResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "compound" => new CompoundResource($this->compound),
            "zone" => new ZoneResource($this->zone),
            "rows" => $this->rows,
            "columns" => $this->columns,
            "capacity" => $this->capacity,
        ];
    }
}
