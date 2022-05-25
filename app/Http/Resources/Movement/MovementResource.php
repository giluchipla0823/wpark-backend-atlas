<?php

namespace App\Http\Resources\Movement;

use App\Helpers\ModelHelper;
use JsonSerializable;
use Illuminate\Http\Request;
use App\Http\Resources\Vehicle\VehicleResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle' => new VehicleResource($this->vehicle),
            'user' => new UserResource($this->user),
            'origin_position' => $this->originResource(),
            'destination_position' => $this->destinationResource(),
            'category' => $this->category,
            'confirmed' => $this->confirmed,
            'canceled' => $this->canceled,
            'manual' => $this->manual,
            'dt_start' => $this->dt_start,
            'dt_end' => $this->dt_end,
            'comments' => $this->comments
        ];
    }

    /**
     * @return mixed
     */
    private function originResource(): mixed
    {
        if (!ModelHelper::isEloquentModelInstance($this->origin_position_type)) {
            return null;
        }

        if (!$model = (new $this->origin_position_type)->find($this->origin_position_id)) {
            return null;
        }

        return ModelHelper::modelHasPropertyResourceType($model, 'movementResource')
            ? new $model->movementResource($model)
            : null;
    }

    /**
     * @return mixed
     */
    private function destinationResource(): mixed
    {
        if (!ModelHelper::isEloquentModelInstance($this->destination_position_type)) {
            return null;
        }

        if (!$model = (new $this->destination_position_type)->find($this->destination_position_id)) {
            return null;
        }

        return ModelHelper::modelHasPropertyResourceType($model, 'movementResource')
            ? new $model->movementResource($model)
            : null;
    }
}
