<?php

namespace App\Repositories\Parking;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface ParkingRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
