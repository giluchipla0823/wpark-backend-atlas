<?php

namespace App\Repositories\Recirculation;

use App\Models\Recirculation;
use App\Repositories\BaseRepository;

class RecirculationRepository extends BaseRepository implements RecirculationRepositoryInterface
{
    public function __construct(Recirculation $model)
    {
        parent::__construct($model);
    }

    /**
     * @param Recirculation $recirculation
     * @return void
     */
    public function updateBack(Recirculation $recirculation): void
    {
        $this->update(['back' => 1], $recirculation->id);
    }
}
