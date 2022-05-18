<?php

namespace App\Services\Application\Dealer;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Dealer\DealerResource;
use App\Models\Dealer;
use App\Repositories\Dealer\DealerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DealerService
{
    /**
     * @var DealerRepositoryInterface
     */
    private $repository;

    public function __construct(
        DealerRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = DealerResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return DealerResource::collection($results)->collection;
    }

    /**
     * @param Dealer $dealer
     * @return DealerResource
     */
    public function show(Dealer $dealer): DealerResource
    {
        return new DealerResource($dealer);
    }

    /**
     * @param array $params
     * @return Dealer
     */
    public function create(array $params): Dealer
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
}
