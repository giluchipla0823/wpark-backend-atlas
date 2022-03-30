<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Compound\CompoundResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
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
            'surname' => $this->surname,
            'email' => $this->email,
            'username' => $this->username,
            'first_login' => $this->first_login,
            'last_login' => $this->last_login,
            'online' => $this->online,
            'last_change_password' => $this->last_change_password,
            'admin_pin' => $this->admin_pin,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->getRoleNames(),
            'compounds' => CompoundResource::collection($this->compounds),
            'devices' => $this->devices()->get(),
            'movements' => $this->movements()->get()
        ];
    }
}
