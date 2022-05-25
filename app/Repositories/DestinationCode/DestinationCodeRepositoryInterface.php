<?php

namespace App\Repositories\DestinationCode;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface DestinationCodeRepositoryInterface extends BaseRepositoryInterface
{
    public function datatables(Request $request): array;
}
