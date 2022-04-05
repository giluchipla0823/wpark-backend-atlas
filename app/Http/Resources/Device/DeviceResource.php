<?php

namespace App\Http\Resources\Device;

use App\Http\Resources\DeviceType\DeviceTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => $this->uuid,
            'version' => $this->version,
            'device_type' => new DeviceTypeResource($this->device_type)
        ];
    }
}
