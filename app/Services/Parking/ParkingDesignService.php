<?php

namespace App\Services\Parking;

use Exception;
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
     * @throws Exception
     */
    public function parkingDesign(array $params): Parking
    {
        // Capacidad en mm por slot
        $capacitymm = 4800;
        // TODO: Ver que patrón se usa en alt_qr, como se genera y añadirlo
        DB::beginTransaction();

        try {
            // Calcular la capacidad del parking
            $params['capacity'] = $params['parking_type_id'] != 3 ? array_sum($params['rows']['slots']) : null;
            $params['fill'] = $params['parking_type_id'] != 3 ? 0 : null;

            // Creación del parking
            $parking = $this->parkingRepository->create($params);

            if ($parking->parking_type_id != 3) {
                //Creación de las filas y slots del parking
                $this->parkingRepository->find($parking->id);

                // Convertimos el  qr para añadir ceros a la izquierda hasta los 3 dígitos
                $params['qr'] = str_pad($params['qr'], 3, '0', STR_PAD_LEFT);

                // Total de filas que tendrá el parking
                $totalRows = $params['rows']['count'];
            }

            if ($parking->parking_type_id == 2) {
                for ($i = 1; $i <= $totalRows; $i++) {
                    // Convertimos el row_numer para añadir ceros a la izquierda hasta los 3 dígitos
                    $row_number = str_pad($i, 3, '0', STR_PAD_LEFT);

                    $row = [
                        'row_number' => $row_number,
                        'parking_id' => $parking->id,
                        'capacity' => 1,
                        'alt_qr' => $i == 1 ? $params['qr'] . '.' . $row_number : null,
                        'active' => 1
                    ];
                    $row = $this->rowRepository->create($row);

                    $slot = [
                        'slot_number' => 1,
                        'row_id' => $row->id,
                        'capacity' => 1
                    ];
                    $slot = $this->slotRepository->create($slot);
                }
            } else if ($parking->parking_type_id == 1) {

                for ($i = 0; $i < $totalRows; $i++) {
                    // Convertimos el row_numer para añadir ceros a la izquierda hasta los 3 dígitos
                    $row_number = str_pad($i + 1, 3, '0', STR_PAD_LEFT);

                    $capacity = $params['rows']['slots'][$i];

                    $row = [
                        'row_number' => $row_number,
                        'parking_id' => $parking->id,
                        'capacity' => $capacity,
                        'capacitymm' => $capacity * $capacitymm,
                        'alt_qr' => $params['qr'] . '.' . $row_number,
                        'active' => 1
                    ];
                    $row = $this->rowRepository->create($row);

                    for ($j = 1; $j <= $capacity; $j++) {
                        $slot = [
                            'slot_number' => $j,
                            'row_id' => $row->id,
                            'capacity' => 1,
                            'capacitymm' => $capacitymm
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
