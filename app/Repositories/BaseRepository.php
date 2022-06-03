<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator|Collection
     */
    public function all(Request $request)
    {
        return $this->model->get();
    }

    public function create(array $params): Model
    {
        return $this->model->create($params);
    }

    public function update(array $params, int $id): ?int
    {
        return $this->model->where('id', $id)->update($params);
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function restore(int $id): ?bool
    {
        return $this->model->withTrashed()->findOrFail($id)->restore();
    }

    /**
     * @param array $params
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Model|null
     */
    public function findBy(array $params, array $orderBy = null, $limit = null, $offset = null): ?Model
    {
        $query = $this->model
            ->whereRowValues(array_keys($params), '=', array_values($params));

        if (!empty($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        if (isset($limit)) {
            $query->limit($limit);
        }

        if (isset($offset)) {
            $query->offset($offset);
        }

        return $query->first();
    }

}
