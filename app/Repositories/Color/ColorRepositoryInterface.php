<?php

namespace App\Repositories\Color;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface ColorRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
