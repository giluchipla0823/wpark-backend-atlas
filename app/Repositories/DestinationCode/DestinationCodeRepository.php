<?php

namespace App\Repositories\DestinationCode;

use App\Helpers\QueryParamsHelper;
use App\Models\DestinationCode;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class DestinationCodeRepository extends BaseRepository implements DestinationCodeRepositoryInterface
{
    public function __construct(DestinationCode $destinationCode)
    {
        parent::__construct($destinationCode);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        if ($countryId = $request->query->get("country_id")) {
            $query = $query->where("country_id", "=", $countryId);
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

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

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
