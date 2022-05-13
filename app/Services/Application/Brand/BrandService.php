<?php

namespace App\Services\Application\Brand;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Brand\BrandResource;
use App\Models\Brand;
use App\Repositories\Brand\BrandRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BrandService
{
    /**
     * @var BrandRepositoryInterface
     */
    private $repository;

    public function __construct(BrandRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = BrandResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return BrandResource::collection($results)->collection;
    }

    /**
     * @param Brand $brand
     * @return BrandResource
     */
    public function show(Brand $brand): BrandResource
    {
        return new BrandResource($brand);
    }

    /**
     * @param array $params
     * @return Brand
     */
    public function create(array $params): Brand
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
