<?php

namespace App\Repositories;

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

    public function all(Request $request): Collection
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
}
