<?php

namespace App\Repositories\Country;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface CountryRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
