<?php

namespace App\Services\Application\DestinationCode;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Models\DestinationCode;
use App\Repositories\DestinationCode\DestinationCodeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DestinationCodeService
{
    /**
     * @var DestinationCodeRepositoryInterface
     */
    private $repository;

    public function __construct(DestinationCodeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        return DestinationCodeResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $resource = DestinationCodeResource::collection($results['data']);

        $results['data'] = $resource->collection;

        return collect($results);
    }

    /**
     * @param DestinationCode $destinationCode
     * @return DestinationCodeResource
     */
    public function show(DestinationCode $destinationCode): DestinationCodeResource
    {
        return new DestinationCodeResource($destinationCode);
    }

    /**
     * @param array $params
     * @return DestinationCode
     */
    public function create(array $params): DestinationCode
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
}
