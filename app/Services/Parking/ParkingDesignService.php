<?php

namespace App\Services\Parking;

use App\Models\Parking;
use App\Repositories\Parking\ParkingRepositoryInterface;
use App\Repositories\Row\RowRepositoryInterface;
use App\Repositories\Slot\SlotRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ParkingDesignService
{

    /**
     * @var ParkingRepositoryInterface
     */
    private $parkingRepository;

    /**
     * @var RowRepositoryInterface
     */
    private $rowRepository;

    /**
     * @var SlotRepositoryInterface
     */
    private $slotRepository;

    public function __construct(
        ParkingRepositoryInterface $parkingRepository,
        RowRepositoryInterface $rowRepository,
        SlotRepositoryInterface $slotRepository
    ) {
        $this->parkingRepository = $parkingRepository;
        $this->rowRepository = $rowRepository;
        $this->slotRepository = $slotRepository;
    }

    /**
     * @param array $params
     * @return Parking
     */
    public function parkingDesign(array $params): Parking
    {
        DB::beginTransaction();

        try {
            // Creación del parking
            $parking = $this->parkingRepository->create($params);

            //Creación de las filas y slots del parking
            $this->parkingRepository->find($parking->id);

            if ($parking->parking_type_id == 2) {
                for ($i = 1; $i <= $parking->capacity; $i++) {
                    $row = [
                        'row_number' => $i,
                        'parking_id' => $parking->id,
                        'block_id' => 1,
                        'capacity' => 1,
                        'capacitymm' => $parking->capacitymm / $parking->capacity,
                        'alt_qr' => '017.' . $parking->id . '.' . $i,
                    ];
                    $row = $this->rowRepository->create($row);

                    $slot = [
                        'slot_number' => 1,
                        'row_id' => $row->id,
                        'capacity' => 1,
                        'capacitymm' => $row->capacitymm
                    ];
                    $slot = $this->slotRepository->create($slot);
                }
            } else if ($parking->parking_type_id == 1) {
                $totalRows = ($parking->end_row - $parking->start_row) + 1;
                for ($i = 1; $i <= $totalRows; $i++) {
                    $row = [
                        'row_number' => $i,
                        'parking_id' => $parking->id,
                        'block_id' => 1,
                        'capacity' => 8,
                        'capacitymm' => $parking->capacitymm / $totalRows,
                        'alt_qr' => '017.' . $parking['id'] . '.' . $i,
                    ];
                    $row = $this->rowRepository->create($row);

                    for ($j = 1; $j <= $row->capacity; $j++) {
                        $slot = [
                            'slot_number' => $j,
                            'row_id' => $row->id,
                            'capacity' => 1,
                            'capacitymm' => $row->capacitymm / $row->capacity
                        ];
                        $slot = $this->slotRepository->create($slot);
                    }
                }
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $parking;
    }
}
