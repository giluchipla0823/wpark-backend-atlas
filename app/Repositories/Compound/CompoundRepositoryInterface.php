<?php

namespace App\Repositories\Compound;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface CompoundRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
