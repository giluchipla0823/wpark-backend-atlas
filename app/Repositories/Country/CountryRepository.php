<?php

namespace App\Repositories\Country;

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
        $query = $this->model->query();

        if ($request->query->getBoolean('can_exclude_unknown')) {
            $query = $query->where("id", "!=", Country::UNKNOWN_ID);
        }

        return $query->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query();

        if ($request->query->getBoolean('can_exclude_unknown')) {
            $query = $query->where("id", "!=", Country::UNKNOWN_ID);
        }

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
