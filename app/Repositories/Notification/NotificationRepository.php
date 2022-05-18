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
        $query = $this->model->query();

        //dd($request);

        return Datatables::customizable($query)->response();
    }

}
