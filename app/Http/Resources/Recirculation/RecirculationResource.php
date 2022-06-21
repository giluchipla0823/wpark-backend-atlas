<?php

namespace App\Http\Resources\Recirculation;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RecirculationResource extends JsonResource
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
            "username" => $this->user->username,
            "message" => $this->message,
            "success" => (bool) $this->success,
            "back" => (bool) $this->back,
            "created_at" => $this->created_at
        ];
    }
}
