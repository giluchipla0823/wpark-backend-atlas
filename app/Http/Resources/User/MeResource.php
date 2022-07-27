<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Compound\CompoundResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'username' => $this->username,
            'first_login' => $this->first_login,
            'last_login' => $this->last_login,
            'online' => $this->online,
            'last_change_password' => $this->last_change_password,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->getRoleNames(),
            'compounds' => CompoundResource::collection($this->compounds),
            'devices' => $this->devices()->get(),
        ];
    }
}
