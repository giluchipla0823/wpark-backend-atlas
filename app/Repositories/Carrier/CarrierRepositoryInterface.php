<?php

namespace App\Repositories\Carrier;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface CarrierRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
