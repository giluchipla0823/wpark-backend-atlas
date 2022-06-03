<?php

namespace App\Repositories\Load;

use App\Models\Load;
use App\Models\Vehicle;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface LoadRepositoryInterface extends BaseRepositoryInterface
{
    public function generate(array $params): Load;
    public function checkVehicles(array $params): array;
    public function datatables(Request $request): array;
    public function datatablesVehicles(Load $load): array;
    public function unlinkVehicle(Load $load, Vehicle $vehicle): void;

}
