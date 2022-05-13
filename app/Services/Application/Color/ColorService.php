<?php

namespace App\Services\Application\Color;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\Color\ColorResource;
use App\Models\Color;
use App\Repositories\Color\ColorRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ColorService
{
    /**
     * @var ColorRepositoryInterface
     */
    private $repository;

    public function __construct(ColorRepositoryInterface $repository)
    {
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

            $resource = ColorResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return ColorResource::collection($results)->collection;
    }

    /**
     * @param Color $color
     * @return ColorResource
     */
    public function show(Color $color): ColorResource
    {
        return new ColorResource($color);
    }

    /**
     * @param array $params
     * @return Color
     */
    public function create(array $params): Color
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
