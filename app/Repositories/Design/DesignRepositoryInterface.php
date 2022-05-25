<?php

namespace App\Repositories\Design;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface DesignRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
