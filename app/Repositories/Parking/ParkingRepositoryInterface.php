<?php

namespace App\Repositories\Parking;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface ParkingRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;

    public function findAllByZone(int $id): Collection;
}
