<?php

namespace App\Repositories\Vehicle;

use App\Models\Row;
use App\Models\State;
use App\Models\Vehicle;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface VehicleRepositoryInterface extends BaseRepositoryInterface
{

    public function findAllByRow(Row $row): Collection;

    public function findAllByState(State $state): Collection;

    public function datatables(Request $request): Collection;

    public function createManual(array $params): Vehicle;

}
