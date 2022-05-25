<?php

namespace App\Repositories\Rule;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface RuleRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
