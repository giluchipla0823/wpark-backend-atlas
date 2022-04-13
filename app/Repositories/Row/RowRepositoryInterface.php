<?php

namespace App\Repositories\Row;

use App\Models\Block;
use App\Models\Parking;
use App\Models\Row;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface RowRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllByBlock(Block $block): Collection;

    public function unlinkBlock(Row $row): void;

    public function updateBlockToRows(Block $block, array $rows): void;

    public function findAllByParking(Parking $parking): Collection;
}
