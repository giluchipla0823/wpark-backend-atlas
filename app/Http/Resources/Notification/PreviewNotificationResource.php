<?php

namespace App\Http\Resources\Notification;

use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class PreviewNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(
            [
                'id' => $this->id,
            ],
            json_decode($this->data, true)
        );
    }

}
