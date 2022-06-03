<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * @param Request $request
     * @return LengthAwarePaginator|Collection
     */
    public function all(Request $request);

    public function create(array $params): Model;

    public function update(array $params, int $id): ?int;

    public function delete(int $id);

    public function find(int $id): ?Model;

    public function restore(int $id): ?bool;

    /**
     * @param array $params
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Model
     */
    public function findBy(array $params, array $orderBy = null, $limit = null, $offset = null) : ?Model;

}
