<?php

namespace App\Services\External;

use App\Repositories\Recirculation\RecirculationRepositoryInterface;
use Exception;
use App\Models\Stage;
use App\Models\Vehicle;
use App\Helpers\XmlHelper;
use App\Repositories\Vehicle\VehicleRepositoryInterface;
use App\Exceptions\owner\BadRequestException;

class RecirculationService
{

    private const REFERENCE_ERROR_CODE = 'recirculation-ws';

    protected $wsdl;

    /**
     * @var VehicleRepositoryInterface
     */
    private $vehicleRepository;

    /**
     * @var RecirculationRepositoryInterface
     */
    private $recirculationRepository;

    public function __construct(
        VehicleRepositoryInterface $vehicleRepository,
        RecirculationRepositoryInterface $recirculationRepository
    )
    {
        $this->wsdl = config("services.ford_services.recirculations.wsdl");
        $this->vehicleRepository = $vehicleRepository;
        $this->recirculationRepository = $recirculationRepository;
    }

    /**
     * @param string $vin
     * @return array
     * @throws BadRequestException
     */
    public function GetVehicleDestination(string $vin): array
    {
        $vehicle = $this->vehicleRepository->findOneByVin($vin);

        // Se debe validar que VIN ingresado se encuentre registrado en nuestra base de datos. Si no existe, lanzar excepción 404 con el mensaje “El vehículo especificado no se encuentra registrado“.
        if(!$vehicle){
            throw new BadRequestException("Recirculaciones: El vehículo especificado no se encuentra registrado.");
        }

//        // Si el vehículo existe, se procede a comprobar en la tabla “vehicles_stages“ si tiene registrado la etapa de “Gate release“. Si el vehículo tiene el registro mencionado en la tabla “vehicles_stages“, se debe lanzar una excepción con código de estado 400 con el siguiente mensaje: “El vehículo ya tiene la aprobación de etapa GATE RELEASE“.
//        foreach($vehicle->stages as $stage){
//            if($stage->code === Stage::STAGE_ST7_CODE){
//                throw new BadRequestException("Recirculaciones: El vehículo ya tiene la aprobación de etapa GATE RELEASE.");
//            }
//        }

        $originPosition = $vehicle->lastMovement && $vehicle->lastMovement->destinationPosition
            ? $vehicle->lastMovement->destinationPosition
            : null;

        if (!$originPosition) {
            throw new BadRequestException("Recirculaciones: El vehículo especificado no tiene movimientos confirmados.");
        }

        // Llamada CURL para ejecutar WSDL
        try {

            $xml_entry = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:veh="VehicleService">
                <soapenv:Header/>
                    <soapenv:Body>
                        <veh:GetVehicleDestination>
                            <Request>
                                <inputVin>'.$vin.'</inputVin>
                            </Request>
                        </veh:GetVehicleDestination>
                    </soapenv:Body>
            </soapenv:Envelope>';

            $response = XmlHelper::execCurl($this->wsdl, $xml_entry);

            $response_xml = $response;

            $array = XmlHelper::parseToArray($response);

            $result = count($array) > 0 ? (array) current($array) : null;

            if (!$result) {
                // El servicio no ha devuelto una respuesta o no se encuentra disponible
                throw new BadRequestException("ERROR PDA", [
                    'parameters' => $xml_entry,
                    'response' => null,
                ]);
            }

            if(!array_key_exists("responseSt7BoardText1", $result) || $result['responseSt7BoardText1'] === 'Error' || empty($result['responseSt7BoardText1'])){

                // No existe información de campo responseSt7BoardText1
                throw new BadRequestException("ERROR PDA TEXTO", [
                    'parameters' => $xml_entry,
                    'response' => $response_xml,
                ]);
            }

            // Registrar recirculación - éxito
            $this->recirculationRepository->create([
                "vehicle_id" => $vehicle->id,
                "message" => $result["responseSt7BoardText1"],
                "origin_position_type" => get_class($originPosition),
                "origin_position_id" => $originPosition->id,
                "success" => 1
            ]);

            return $result;

        } catch (Exception $e) {

            /**
             * Otro de los casos de error que puede darse es “Connection timeout“, lo cual suele ocurrir entre los
             * 6 y 10 seg. de demora
             */
            if ($e instanceof BadRequestException) {
                $this->saveLog($vehicle, $e->getMessage(), $e->getExtras());
            } else {
                $this->saveLog($vehicle, "ERROR PDA", [
                    'parameters' => $xml_entry,
                    'response' => null,
                    'message' => "Connection timeout",
                ]);
            }

            // Registrar recirculación - error
            $this->recirculationRepository->create([
                "vehicle_id" => $vehicle->id,
                "message" => "ERROR QLS GENERICO",
                "origin_position_type" => get_class($originPosition),
                "origin_position_id" => $originPosition->id,
                "success" => 0
            ]);

            throw new BadRequestException("ERROR QLS GENERICO.", ['error_detail' => $e->getMessage()]);
        }

    }


    /**
     * @param Vehicle $vehicle
     * @param string $message
     * @param array $properties
     * @return void
     */
    private function saveLog(Vehicle $vehicle, string $message, array $properties): void
    {
        $activity = activity('Recirculaciones')
            ->withProperties($properties)
            ->log($message);
        $activity->reference_code = self::REFERENCE_ERROR_CODE;
        $activity->subject_type = get_class($vehicle);
        $activity->subject_id = $vehicle->id;
        $activity->save();
    }

}
