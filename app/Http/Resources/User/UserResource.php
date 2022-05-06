<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Device\DeviceResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Compound\CompoundResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'username' => $this->username,
            'last_login' => $this->last_login,
            'compounds' => CompoundResource::collection($this->compounds),
        ];

        if (in_array('devices', $relationships)) {
            $response['devices'] = DeviceResource::collection($this->devices);
        }

        return $response;
    }
}
