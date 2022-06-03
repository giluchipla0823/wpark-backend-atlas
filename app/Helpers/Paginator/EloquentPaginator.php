<?php

namespace App\Helpers\Paginator;

use App\Helpers\JsonResourceHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentPaginator
{

    /**
     * @var LengthAwarePaginator
     */
    private $paginator;

    /**
     * @var string
     */
    private $resource;

    public function __construct(LengthAwarePaginator $paginator, string $resource)
    {
        $this->paginator = $paginator;
        $this->resource = $resource;
    }

    public function collection(): Collection
    {
        return collect([
            "current_page" => $this->paginator->currentPage(),
            "data" => $this->items(),
            "per_page" => $this->paginator->perPage(),
            "last_page" => $this->paginator->lastPage(),
            "total" => $this->paginator->total(),
        ]);
    }

    /**
     * @return array
     */
    private function items(): array
    {
        if (JsonResourceHelper::isInstance($this->resource)) {
            return $this->resource::collection($this->paginator->items())->collection->toArray();
        }

        return $this->paginator->items();
    }
}
