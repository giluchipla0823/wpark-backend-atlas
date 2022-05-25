<?php

namespace App\Repositories\Area;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface AreaRepositoryInterface extends BaseRepositoryInterface
{
     public function datatables(Request $request): array;
}
