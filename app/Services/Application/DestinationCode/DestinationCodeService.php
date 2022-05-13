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

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = DestinationCodeResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return DestinationCodeResource::collection($results)->collection;
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
