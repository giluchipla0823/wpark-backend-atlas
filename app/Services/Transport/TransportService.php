<?php

namespace App\Services\Transport;

use App\Models\Transport;
use App\Repositories\Transport\TransportRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Transport\TransportResource;

class TransportService
{
    /**
     * @var TransportRepositoryInterface
     */
    private $repository;

    public function __construct(TransportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = TransportResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return TransportResource::collection($results)->collection;
    }

    /**
     * @param Transport $transport
     * @return TransportResource
     */
    public function show(Transport $transport): TransportResource
    {
        return new TransportResource($transport);
    }

    /**
     * @param array $params
     * @return Transport
     */
    public function create(array $params): Transport
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

    public function restore(int $id): void
    {
        $this->repository->restore($id);
    }

    /**
     * @param Transport $transport
     * @return int
     */
    public function toggleActive(Transport $transport): int {
        $active = $transport->active ? 0 : 1;

        $this->update(['active' => $active], $transport->id);

        return $active;
    }
}
