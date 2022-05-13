<?php

namespace App\Services\Application\Country;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Country\CountryResource;
use App\Models\Country;
use App\Repositories\Country\CountryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CountryService
{
    /**
     * @var CountryRepositoryInterface
     */
    private $repository;

    public function __construct(CountryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = CountryResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return CountryResource::collection($results)->collection;
    }

    /**
     * @param Country $country
     * @return CountryResource
     */
    public function show(Country $country): CountryResource
    {
        return new CountryResource($country);
    }

    /**
     * @param array $params
     * @return Country
     */
    public function create(array $params): Country
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
