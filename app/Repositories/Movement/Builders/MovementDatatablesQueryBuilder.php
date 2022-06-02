<?php

namespace App\Repositories\Movement\Builders;

use App\Models\Movement;
use App\Models\Parking;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MovementDatatablesQueryBuilder
{
    /**
     * @var Movement
     */
    private $model;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Builder
     */
    private $query;

    public function __construct(Movement $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        $this->query = $this->model->query();

        $this->addFilters();

        return $this->query;
    }

    /**
     * @return void
     */
    private function addFilters(): void
    {
        // Vins filter
        if ($vins = $this->request->get('vins')) {
            $this->query = $this->query->whereHas('vehicle', function ($q) use ($vins) {
                $vinValues = explode(',', $vins['value']);

                if ($vins['filter_type'] === "not_equal") {
                    return $q->whereNotIn('vin', $vinValues);
                } else {
                    return $q->whereIn('vin', $vinValues);
                }
            });
        }

        // Users filter
        if ($users = $this->request->get('users')) {
            $users = explode(',', $users);

            $this->query = $this->query->whereHas('user', function ($q) use ($users) {
                return $q->whereIn('id', $users);
            });
        }

        // Origins parkings filter
        if ($originsParkings = $this->request->get('origins_parkings')) {
            $originsParkings = explode(',', $originsParkings);

            $this->query = $this->query->whereHasMorph('originPosition', [Parking::class, Slot::class], function ($q, $type) use ($originsParkings) {
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
        if ($destinationsParkings = $this->request->get('destinations_parkings')) {
            $destinationsParkings = explode(',', $destinationsParkings);

            $this->query = $this->query->whereHasMorph('destinationPosition', [Parking::class, Slot::class], function ($q, $type) use ($destinationsParkings) {
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
        if ($dates = $this->request->get('created_at')) {
            $dates = explode(' - ', $dates);

            $dateTo = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
            $dateFrom = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

            $this->query->whereDate('created_at', '>=', $dateTo)->where('created_at', '<=', $dateFrom);
        }
    }
}
