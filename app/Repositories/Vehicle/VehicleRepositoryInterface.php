<?php

namespace App\Repositories\Vehicle;

use App\Models\Row;
use App\Models\State;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface VehicleRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllByRow(Row $row): Collection;

    public function findAllByState(State $state): Collection;

    public function datatables(Request $request): array;

}
