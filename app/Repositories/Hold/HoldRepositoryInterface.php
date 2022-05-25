<?php

namespace App\Repositories\Hold;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface HoldRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
