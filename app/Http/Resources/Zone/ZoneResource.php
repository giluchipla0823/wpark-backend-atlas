<?php

namespace App\Http\Resources\Zone;

use App\Models\Zone;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "parking_types_available" => $this->getParkingTypesAvailable()
        ];
    }
}
