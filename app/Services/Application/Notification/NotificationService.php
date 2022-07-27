<?php

namespace App\Services\Application\Notification;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Notification\NotificationResource;
use App\Http\Resources\Notification\PreviewNotificationResource;
use App\Models\Notification;
use App\Repositories\Notification\NotificationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * @var NotificationRepositoryInterface
     */
    private $repository;

    public function __construct(NotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        return PreviewNotificationResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $resource = NotificationResource::collection($results['data']);

        $results['data'] = $resource->collection->toArray();

        return collect($results);
    }

    /**
     * @param Notification $notification
     * @return NotificationResource
     */
    public function show(Notification $notification): NotificationResource
    {
        $notification->load(QueryParamsHelper::getIncludesParamFromRequest());

        return new NotificationResource($notification);
    }

    /**
     * @param array $params
     * @return Notification
     */
    public function create(array $params): Notification
    {
        return $this->repository->create($params);
    }

    /**
     * @param array $params
     * @param int $id
     * @return void
     */
    public function update(array $params, int $id): void
    {
        $this->repository->update($params, $id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function restore(int $id): void
    {
        $this->repository->restore($id);
    }

    /**
     * @param Notification $notification
     * @return void
     */
    public function toggleSeen(Notification $notification): void
    {
        $value = $notification->reat_at ? null : Carbon::now();

        $this->update(["reat_at" => $value], $notification->id);
    }

}
