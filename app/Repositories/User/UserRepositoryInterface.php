<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Http\Request;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findOneByUsername(string $username): ?User;

    public function datatables(Request $request): array;
}
