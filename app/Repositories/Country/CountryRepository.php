<?php

namespace App\Repositories\Country;

use App\Helpers\QueryParamsHelper;
use App\Models\Country;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct(Country $country)
    {
        parent::__construct($country);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        return $this->model->all();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query();

        return Datatables::customizable($query)->response();
    }

    /**
     * @param string $code
     * @return Model|null
     */
    public function findByCode(string $code): ?Model
    {
        return $this->findBy(['code' => $code]);
    }
}
