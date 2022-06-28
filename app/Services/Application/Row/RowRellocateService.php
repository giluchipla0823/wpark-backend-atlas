<?php

namespace App\Services\Application\Row;

use Exception;
use App\Models\Row;
use App\Models\Slot;
use App\Models\Zone;
use App\Models\Parking;
use App\Models\Vehicle;
use App\Models\Movement;
use App\Models\ParkingType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exceptions\owner\NotFoundException;
use App\Exceptions\owner\BadRequestException;
use App\Services\Application\Movement\MovementService;
use App\Repositories\Parking\ParkingRepositoryInterface;
use App\Repositories\Movement\MovementRepositoryInterface;

class RowRellocateService
{
    /**
     * @var MovementRepositoryInterface
     */
    private $movementRepository;
    /**
     * @var ParkingRepositoryInterface
     */
    private $parkingRepository;

    /**
     * @var MovementService $movementService
     */
    private $movementService;

    public function __construct(
        MovementRepositoryInterface $movementRepository,
        ParkingRepositoryInterface $parkingRepository
    )
    {
        $this->movementRepository = $movementRepository;
        $this->parkingRepository = $parkingRepository;

        $this->movementService = app()->make(MovementService::class);
    }

    /**
     * @param Row $row
     * @param array $params
     * @return void
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function process(Row $row, array $params): void
    {
        if ($row->parking->parkingType->id !== ParkingType::TYPE_ROW) {
            throw new BadRequestException("La fila {$row->row_name} debe pertenecer a un parking de tipo FILAS.");
        }

        if (!$row->rule) {
            throw new BadRequestException("La fila {$row->row_name} no tiene ninguna regla empezada.");
        }

        $buffer = $this->parkingRepository->findAllByZone(Zone::BUFFER)->first();

        if (!$buffer) {
            throw new NotFoundException('La campa actual no tiene asignada un parking de zona buffer.');
        }

        $vehiclesRow = collect($params['row_vehicles'] ?? []);
        $vehiclesBuffer = collect($params['buffer_vehicles'] ?? []);

        $vehiclesIds = [];

        foreach ($vehiclesRow as $value) {
            $vehiclesIds[] = $value["vehicle_id"];
        }

        foreach ($vehiclesBuffer as $value) {
            $vehiclesIds[] = $value["vehicle_id"];
        }

        $vehiclesIds = array_unique($vehiclesIds);
        $rowDestinationIds = $vehiclesRow->pluck("destination_position.id")->toArray();
        $rowSlotsIds = $row->slots->pluck("id")->toArray();

        foreach ($rowDestinationIds as $key => $value) {
            if (!in_array($value, $rowSlotsIds)) {
                throw new BadRequestException(sprintf(
                    "La posición destino de fila Nº %s no pertenece a la fila especificada.",
                    ($key + 1)
                ));
            }
        }

        $checkVehiclesToBuffer = [];

        /**
         * Mapear vehículos de cada slot posicionados en la fila y verificar cuales son los que deben ir a
         * zona de Buffer.
         */
        $this->mappingVehiclesForSlotsAndCheckVehiclesToBuffer($vehiclesRow, $row, $checkVehiclesToBuffer);

        // Validación de vehículos para buffer
        $this->validationVehiclesToBuffer($vehiclesBuffer, $row, $checkVehiclesToBuffer);

        unset($checkVehiclesToBuffer);

        $vehicles = Vehicle::with(['design'])
            ->whereIn('id', $vehiclesIds)
            ->get();

        $vehiclesRow = $this->mappingVehiclesDataWithEloquentModel($vehiclesRow, $vehicles);
        $vehiclesBuffer = $this->mappingVehiclesDataWithEloquentModel($vehiclesBuffer, $vehicles, $buffer);
        $rowRule = $row->rule;

        DB::beginTransaction();

        $usedSlots = $row->slots->filter(function($slot) {
            return $slot->vehicle !== null;
        });

        try {

            // Limpiamos toda los slots con vehículos de la fila seleccionada
            foreach ($usedSlots as $slot) {
                $vehicle = $slot->vehicle;
                unset($slot->vehicle);

                $slot->release($vehicle->design->length);
            }

            // Asignamos los vehículos confirmados a la fila
            foreach ($vehiclesRow as $item) {
                $vehicle = $item["vehicle"];
                $originPosition = $item['origin_position'];
                $destinationPosition = $item['destination_position'];

                if (get_class($originPosition) === Slot::class && get_class($destinationPosition) === Slot::class) {
                    /**
                     * Comprobamos que si las posiciones de origen y destino corresponden a la misma fila,
                     * se procede a reservar espacio.
                     */
                    if ($originPosition->row->id === $destinationPosition->row->id) {
                        $destinationPosition->reserve($vehicle->design->length);

                        $row = $destinationPosition->row;
                        $row->rule_id = $rowRule->id;
                        $row->save();

                        continue;
                    }

                    /**
                     * Comprobamos si las posiciones de origen y destino corresponden a filas diferentes.
                     */
                    if ($originPosition->row->id !== $destinationPosition->row->id) {
                        $originPosition->release($originPosition->fillmm);
                    }
                }

                /**
                 * Si la posición de origen es de tipo "Parking", se procede a eliminar el espacio ocupado
                 * en el parking.
                 */
                if (get_class($originPosition) === Parking::class) {
                    $originPosition->release();
                }

                $this->saveRellocateData($row, $item);
            }

            // Asignamos los vehículos confirmados a la zona buffer
            foreach ($vehiclesBuffer as $item) {
                $this->saveRellocateData($row, $item);
            }

            DB::commit();
        } catch (Exception $exc) {
            DB::rollBack();

            throw $exc;
        }
    }

    /**
     * @param Row $row
     * @param array $item
     * @return void
     * @throws BadRequestException
     * @throws Exception
     */
    private function saveRellocateData(Row $row, array $item): void
    {
        $vehicle = $item["vehicle"];
        $originPosition = $item['origin_position'];
        $destinationPosition = $item['destination_position'];

        $movement = $this->movementService->doMovement($vehicle, [
            "vehicle_id" => $vehicle->id,
            "origin_position_type" => get_class($originPosition),
            "origin_position_id" => $originPosition->id,
            "destination_position_type" => get_class($destinationPosition),
            "destination_position_id" => $destinationPosition->id,
            "manual" => 1,
            "comments" => "Movement by Row Rellocate"
        ]);

        $this->movementService->confirmMovement($movement, false);
    }

    /**
     * @param Collection $collection
     * @param Collection $vehicles
     * @param Parking|null $buffer
     * @return Collection
     */
    private function mappingVehiclesDataWithEloquentModel(
        Collection $collection,
        Collection $vehicles,
        ?Parking $buffer = null
    ): Collection
    {
        if ($collection->isEmpty()) {
            return $collection;
        }

        $validateOriginPositions = [];
        $validateDestinationPositions = [];
        $validateVehicles = [];

        $collection = $collection->map(
            function($value, $index) use ($vehicles, $buffer, &$validateOriginPositions, &$validateDestinationPositions, &$validateVehicles) {
                $vehicle = $vehicles->filter(function($vehicle) use ($value) {
                    return $vehicle->id === $value['vehicle_id'];
                })->first();

                unset($value['vehicle_id']);

                $value["index"] = $index;
                $value['vehicle'] = $vehicle;

                $belongsToBuffer = !is_null($buffer);

                $this->checkDuplicateVehicle($value, $belongsToBuffer, $validateVehicles);

                $this->checkDuplicatePosition($value, $belongsToBuffer, "origin_position", $validateOriginPositions);

                if (!$belongsToBuffer) {
                    $this->checkDuplicatePosition($value, false, "destination_position", $validateDestinationPositions);
                }

                $this->movementService->checkValidateVehicleCurrentPosition($vehicle, $value["origin_position"]);

                return [
                    'vehicle' => $vehicle,
                    'origin_position' => $this->getPosition($value["origin_position"]),
                    'destination_position' => $buffer ?: $this->getPosition($value["destination_position"]),
                ];
            }
        );

        unset($validateOriginPositions);
        unset($validateDestinationPositions);

        return $collection;
    }

    /**
     * @param array $position
     * @return Parking|Slot|null
     * @throws NotFoundException
     */
    private function getPosition(array $position)
    {
        $id = $position["id"];
        $type = $position["type"];
        $data = null;

        switch ($type) {
            case Slot::class:
                $data = Slot::find($id);
                break;

            case Parking::class:
                $data = Parking::find($id);
                break;
        }

        if (!$data) {
            throw new NotFoundException(sprintf(
                "No se existe información del %s con el id %s.",
                strtolower(class_basename($type)),
                $id)
            );
        }

        return $data;
    }

    /**
     * @param array $item
     * @param bool $belongsToBuffer
     * @param array $validateVehicles
     * @return void
     * @throws BadRequestException
     */
    private function checkDuplicateVehicle(array $item, bool $belongsToBuffer, array &$validateVehicles): void
    {
        $index = $item["index"] + 1;
        $vehicle = $item["vehicle"];

        if (in_array($vehicle->id, $validateVehicles)) {
            throw new BadRequestException(sprintf(
                "El vehículo Nº %s con vin %s ya está asignado para reubicar en %s.",
                $index,
                $vehicle->vin,
                $belongsToBuffer ? "Buffer" : "Fila"
            ));
        }

        $validateVehicles[] = $vehicle->id;
    }

    /**
     * @param array $item
     * @param bool $belongsToBuffer
     * @param string $column
     * @param array $validatePositions
     * @return void
     * @throws BadRequestException
     */
    private function checkDuplicatePosition(
        array $item,
        bool $belongsToBuffer,
        string $column,
        array &$validatePositions
    ): void {
        $index = $item["index"] + 1;
        $vehicle = $item["vehicle"];
        $originId = $item[$column]["id"];
        $originType = $item[$column]["type"];

        $exists = count(array_filter($validatePositions, function($value) use ($originId, $originType) {
                return $value["id"] === $originId && $value["type"] === $originType;
            })) > 0;

        if ($exists) {
            throw new BadRequestException(sprintf(
                "El vehículo Nº %s con vin %s tiene asignado una %s que ya tiene asignado otro vehículo para reubicar en %s.",
                $index,
                $vehicle->vin,
                $column === "origin_position" ? "posición origen" : "posición destino",
                $belongsToBuffer ? "Buffer" : "Fila"
            ));
        }

        $validatePositions[] = $item[$column];
    }


    /**
     * @throws BadRequestException
     */
    private function validationVehiclesToBuffer(Collection $vehicles, Row $row, array $checkVehiclesToBuffer) {
        $bufferOriginIds = $vehicles->pluck("origin_position.id")->toArray();
        $rowSlotsIds = $row->slots->pluck("id")->toArray();
        $count = count($checkVehiclesToBuffer);

        /**
         * Verificar que vehículos que figuran en la fila seleccionada no han sido confirmados y
         * deben ser especificados para enviar a Buffer.
         */
        if ($count > 0) {
            $checkVinsToBuffer = implode(",", array_unique(array_column($checkVehiclesToBuffer, "vin")));
            $checkIdsToBuffer = array_unique(array_column($checkVehiclesToBuffer, "id"));

            if ($vehicles->isEmpty()) {
                throw new BadRequestException(sprintf(
                    "Debe especificar %s vehículo(s) para zona de buffer. Los vehículos requeridos son: %s",
                    $count,
                    $checkVinsToBuffer
                ));
            }

            foreach ($vehicles as $index => $value) {
                if (!in_array($value["vehicle_id"], $checkIdsToBuffer)) {
                    throw new BadRequestException(sprintf(
                        "El vehículo Nº %s no coincide con los vehículos requeridos para buffer. Los vehículos requeridos son: %s",
                        ($index + 1),
                        $checkVinsToBuffer
                    ));
                }
            }
        }

        // Verificar que las posiciones de origen de buffer coinciden con slots de la fila seleccionada.
        foreach ($bufferOriginIds as $key => $value) {
            if (!in_array($value, $rowSlotsIds)) {
                throw new BadRequestException(sprintf(
                    "La posición origen de buffer Nº %s no pertenece a la fila especificada.",
                    ($key + 1)
                ));
            }
        }
    }

    /**
     * @param Collection $vehicles
     * @param Row $row
     * @param array $checkVehiclesToBuffer
     * @return void
     */
    private function mappingVehiclesForSlotsAndCheckVehiclesToBuffer(
        Collection $vehicles,
        Row $row,
        array &$checkVehiclesToBuffer
    ): void {
        $vehiclesIds = $vehicles->pluck("vehicle_id")->toArray();

        $checkVehiclesToBuffer = [];

        // Obtener los vehículos que se encuentra posicionados en los slots de la fila.
        foreach ($row->slots as $key => $slot) {
            $row->slots[$key]->vehicle = null;

            if ($slot->fill === 0) {
                continue;
            }

            /* @var Movement $lastMovement */
            $lastMovement = Movement::where([
                ["destination_position_type", "=", Slot::class],
                ["destination_position_id", "=", $slot->id],
                ["canceled", "=", 0]
            ])->latest()->first();

            if ($lastMovement && $lastMovement->confirmed === 1) {
                $vehicle = $lastMovement->vehicle;

                if (!in_array($vehicle->id, $vehiclesIds)) {
                    $checkVehiclesToBuffer[] = $vehicle->toArray();
                }

                $row->slots[$key]->vehicle = $vehicle;
            }
        }
    }
}

