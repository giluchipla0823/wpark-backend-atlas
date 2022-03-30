<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function all(Request $request): Collection;

    public function create(array $params): Model;

    public function update(array $params, int $id): ?int;

    public function delete(int $id);

    public function find(int $id): ?Model;

    public function restore(int $id): ?bool;
}
