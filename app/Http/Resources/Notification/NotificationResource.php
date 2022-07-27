<?php

namespace App\Http\Resources\Notification;

use App\Helpers\ModelHelper;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'sender' => new UserResource($this->sender),
            'recipient' => new UserResource($this->recipient),
            'type' => $this->type,
            'resource' => $this->includeResource(),
            'reference_code' => $this->reference_code,
            'data' => $this->data,
            'reat_at' => $this->reat_at,
            'created_at' => $this->created_at,
            'seen' => $this->seen
        ];
    }

    /**
     * @return mixed
     */
    private function includeResource(): mixed
    {
        if (!ModelHelper::isEloquentModelInstance($this->resourceable_type)) {
            return null;
        }

        if (!$model = (new $this->resourceable_type)->find($this->resourceable_id)) {
            return null;
        }

        return ModelHelper::modelHasPropertyResourceType($model, 'notificationResource')
            ? new $model->notificationResource($model)
            : null;
    }
}
