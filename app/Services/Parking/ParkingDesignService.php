<?php

namespace App\Services\Parking;

use App\Models\Block;
use App\Models\ParkingType;
use Exception;
use App\Models\Parking;
use App\Models\Row;
use App\Models\Slot;
use App\Models\Zone;
use App\Models\Area;
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

        DB::beginTransaction();

        try {
            /* Comprobamos si el parking va a ser de presorting o no a través del área,
            si es de presorting cambiamos el campo "presorting" a true para automáticamente
            ponerle el bloque de presorting a las filas de ese parking */
            $presorting = false;
            $presorting_block = null;

            $area = Area::where('id', $params['area_id'])->first();

            if($area->zone_id == Zone::PRESORTING){
                $presorting = true;

                $presorting_block = Block::where([
                    ['is_presorting', '=' , 1,],
                    ['presorting_default', '=' , 1,]
                ])->first();

                if (null === $presorting_block) {
                    $presorting_block = Block::where('is_presorting', 1)->first();
                }
            }

            // Calcular la capacidad del parking
            $params['capacity'] = 0;
            foreach($params['rows'] as $row){
                $params['capacity'] += $row['slots'];
            }
            $params['capacity'] = $params['parking_type_id'] != ParkingType::TYPE_UNLIMITED ? $params['capacity'] : null;
            $params['fill'] = $params['parking_type_id'] != ParkingType::TYPE_UNLIMITED ? 0 : null;

            // Creación del parking
            $parking = $this->parkingRepository->create($params);

            if ($parking->parking_type_id != ParkingType::TYPE_UNLIMITED) {
                //Creación de las filas y slots del parking
                $this->parkingRepository->find($parking->id);

                // Convertimos el  qr para añadir ceros a la izquierda hasta los 3 dígitos
                $params['qr'] = str_pad($params['qr'], 3, '0', STR_PAD_LEFT);

            }

            if ($parking->parking_type_id == ParkingType::TYPE_ESPIGA) {
                foreach ($params['rows'] as $index => $fila) {

                    // Nos interesa que el índice empiece en 1 en lugar de 0 para establecer los row_number
                    $index = $index + 1;

                    // // Convertimos el row_number para añadir ceros a la izquierda hasta los 3 dígitos
                    // $row_number = str_pad($index, 3, '0', STR_PAD_LEFT);
                    $row_number = $index;

                    // Establecemos la capacidad de las filas de espiga que siempre será 1
                    $capacity = Row::ESPIGA_CAPACITY;

                    $row = [
                        'row_number' => $row_number,
                        'parking_id' => $parking->id,
                        'block_id' => !$presorting ? $fila['block_id'] : $presorting_block->id,
                        'capacity' => $capacity,
                        'capacitymm' => $capacity * Slot::CAPACITY_MM,
                        'alt_qr' => $index == 1 ? $params['qr'] . '.' . $row_number : null,
                        'active' => 1
                    ];
                    $row = $this->rowRepository->create($row);

                    $slot = [
                        'slot_number' => 1,
                        'row_id' => $row->id,
                        'capacity' => $capacity
                    ];
                    $slot = $this->slotRepository->create($slot);
                }
            }
            else if ($parking->parking_type_id == ParkingType::TYPE_ROW) {

                foreach ($params['rows'] as $index => $fila) {

                    // Nos interesa que el índice empiece en 1 en lugar de 0 para establecer los row_number
                    $index = $index + 1;
//                    // Convertimos el row_numer para añadir ceros a la izquierda hasta los 3 dígitos
//                    $row_number = str_pad($index, 3, '0', STR_PAD_LEFT);
                    $row_number = $index;

                    $row = [
                        'row_number' => $row_number,
                        'parking_id' => $parking->id,
                        'block_id' => !$presorting ? $fila['block_id'] : $presorting_block->id,
                        'capacity' => $fila['slots'],
                        'capacitymm' => $fila['slots'] * Slot::CAPACITY_MM,
                        'alt_qr' => $params['qr'] . '.' . $row_number,
                        'active' => 1
                    ];

                    $row = $this->rowRepository->create($row);

                    for ($j = 1; $j <= $fila['slots']; $j++) {
                        $slot = [
                            'slot_number' => $j,
                            'row_id' => $row->id,
                            'capacity' => 1,
                            'capacitymm' => Slot::CAPACITY_MM
                        ];
                        $slot = $this->slotRepository->create($slot);
                    }
                }
            }

            DB::commit();
        } catch (Exception $exc) {
            DB::rollback();

            throw $exc;
        }

        return $parking;
    }
}
