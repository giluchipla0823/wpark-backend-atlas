<?php

namespace App\Repositories\Notification;

use App\Helpers\QueryParamsHelper;
use App\Models\Block;
use App\Models\Notification;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        if ($referenceCode = $request->get('reference_code')) {
            $query = $query->where('reference_code', $referenceCode);
        }

        if ($request->query->has("seen")) {
            $query = $request->query->getBoolean("seen")
                ? $query->whereNotNull("reat_at")
                : $query->whereNull("reat_at");
        }

        $limit = $request->get('limit', 5);
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query = $query->orderBy($sortBy, $sortDirection)->take($limit);

        return $query->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {

        $query = $this->model->query()
                    ->with(QueryParamsHelper::getIncludesParamFromRequest())
                    ->select(['notifications.*']);

        if ($request->query->has("seen")) {
            $query = $request->query->getBoolean("seen")
                ? $query->whereNotNull("reat_at")
                : $query->whereNull("reat_at");
        }

        if ($referenceCode = $request->get('reference_code')) {
            $query = $query->where('reference_code', $referenceCode);

            if ($referenceCode === Notification::ROW_COMPLETED) {
                $query = $query
                            ->join('rows', 'notifications.resourceable_id', '=', 'rows.id')
                            ->join('parkings', 'rows.parking_id', '=', 'parkings.id')
                            ->addSelect([
                                DB::raw("CONCAT(parkings.name, '.', LPAD(rows.row_number, 3, '0')) AS row_name")
                            ]);
            }
        }

        return Datatables::customizable($query)->response();
    }

}
