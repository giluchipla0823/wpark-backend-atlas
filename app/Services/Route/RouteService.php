<?php

namespace App\Services\Route;

use App\Models\Route;
use App\Repositories\Route\RouteRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Route\RouteResource;

class RouteService
{
    /**
     * @var RouteRepositoryInterface
     */
    private $repository;

    public function __construct(RouteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = RouteResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return RouteResource::collection($results)->collection;
    }

    /**
     * @param Route $route
     * @return RouteResource
     */
    public function show(Route $route): RouteResource
    {
        return new RouteResource($route);
    }

    /**
     * @param array $params
     * @return Route
     */
    public function create(array $params): Route
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
