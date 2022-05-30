<?php

namespace App\Repositories\Carrier;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CarrierRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;

    public function findAllByRouteTypeId(int $routeTypeId): Collection;
}
