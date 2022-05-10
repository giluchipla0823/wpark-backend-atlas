<?php

namespace App\Repositories\Block;

use App\Repositories\BaseRepositoryInterface;

interface BlockRepositoryInterface extends BaseRepositoryInterface
{
    public function removeAllPresortingDefault(): void;
}
