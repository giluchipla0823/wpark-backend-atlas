<?php

namespace App\Repositories\Movement;

use App\Helpers\QueryParamsHelper;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\Slot;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class MovementRepository extends BaseRepository implements MovementRepositoryInterface
{
    public function __construct(Movement $movement)
    {
        parent::__construct($movement);
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        return $query->get();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function datatables(Request $request): array
    {
        $query = $this->model->query();

        // Filters

        // Vins filter
        if ($vehicles = $request->get('vins')) {
            $vehicles = explode(',', $vehicles);

            $query = $query->whereHas('vehicle', function ($q) use ($vehicles) {
                return $q->whereIn('vin', $vehicles);
            });
        }

        // Users filter
        if ($users = $request->get('users')) {
            $users = explode(',', $users);

            $query = $query->whereHas('user', function ($q) use ($users) {
                return $q->whereIn('id', $users);
            });
        }

        // Origins parkings filter
        if ($originsParkings = $request->get('origins_parkings')) {
            $originsParkings = explode(',', $originsParkings);

            $query = $query->whereHasMorph('originPosition', [Parking::class, Slot::class], function ($q, $type) use ($originsParkings) {
                if ($type === Parking::class) {
                    $q->whereIn('id', $originsParkings);
                } else if ($type === Slot::class) {
                    $q->whereHas('row', function ($q2) use ($originsParkings) {
                        $q2->whereHas('parking', function ($q3) use ($originsParkings) {
                            $q3->whereIn('id', $originsParkings);
                        });
                    });
                }
                return $q;
            });
        }

        // Destinations parkings filter
        if ($destinationsParkings = $request->get('destinations_parkings')) {
            $destinationsParkings = explode(',', $destinationsParkings);

            $query = $query->whereHasMorph('destinationPosition', [Parking::class, Slot::class], function ($q, $type) use ($destinationsParkings) {
                if ($type === Parking::class) {
                    $q->whereIn('id', $destinationsParkings);
                } else if ($type === Slot::class) {
                    $q->whereHas('row', function ($q2) use ($destinationsParkings) {
                        $q2->whereHas('parking', function ($q3) use ($destinationsParkings) {
                            $q3->whereIn('id', $destinationsParkings);
                        });
                    });
                }
                return $q;
            });
        }

        // Created_at filter
        if ($dates = $request->get('created_at')) {
            $dates = explode(' - ', $dates);

            $dateTo = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
            $dateFrom = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

            $query->whereDate('created_at', '>=', $dateTo)->where('created_at', '<=', $dateFrom);
        }

        return Datatables::customizable($query)->response();
    }
}
