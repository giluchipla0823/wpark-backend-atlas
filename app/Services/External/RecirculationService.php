<?php

namespace App\Services\External;

use App\Models\Stage;
use App\Models\Vehicle;
use App\Helpers\XmlHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Exceptions\owner\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecirculationService
{

    protected $wsdl;

    public function __construct(){
        $this->wsdl = config("services.ford_services.recirculations.wsdl");
    }

    /**
     * @param string $vin
     * @return false|mixed
     * @throws BadRequestException
     */
    public function GetVehicleDestination(string $vin){

        $vehicle = Vehicle::where('vin', $vin)->first();

        // Se debe validar que VIN ingresado se encuentre registrado en nuestra base de datos. Si no existe, lanzar excepción 404 con el mensaje “El vehículo especificado no se encuentra registrado“.
        if(!isset($vehicle)){
            throw new BadRequestException("Recirculaciones: El vehículo especificado no se encuentra registrado.");
        }

        // Si el vehículo existe, se procede a comprobar en la tabla “vehicles_stages“ si tiene registrado la etapa de “Gate release“. Si el vehículo tiene el registro mencionado en la tabla “vehicles_stages“, se debe lanzar una excepción con código de estado 400 con el siguiente mensaje: “El vehículo ya tiene la aprobación de etapa GATE RELEASE“.
        foreach($vehicle->stages as $stage){
            if($stage->code === Stage::STAGE_ST7_CODE){
                throw new BadRequestException("Recirculaciones: El vehículo ya tiene la aprobación de etapa GATE RELEASE.");
            }
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

            if(isset($array[0])){
                if(isset($array[0]->responseSt7BoardText1) && empty($array[0]->responseSt7BoardText1)){

                    /**
                     * Guardar log en tabla “activity_log“. La información de los campos de esta será:
                     * log_name: Recirculaciones
                     * log_description: No existe información de campo responseSt7BoardText1
                     * subject_type: recirculation-ws
                     * properties: Guardar en formato json tanto el XML de entrada como el de respuesta. Por ejemplo:
                     */

                    $activity = activity('Recirculaciones')
                        ->withProperties([
                            'parameters' => $xml_entry,
                            'response' => $response_xml,
                        ])
                        ->log('No existe información de campo responseSt7BoardText1');
                    $activity->description = 'No existe información de campo responseSt7BoardText1';
                    $activity->subject_type = 'recirculation-ws';
                    $activity->save();

                    throw new BadRequestException("ERROR QLS GENERICO.");
                }
            }else{

                /**
                 * Error si el servicio no devuelve nada
                 * Guardar log en tabla “activity_log“. La información de los campos de esta será:
                 * log_name: Recirculaciones
                 * log_description: Servicio no disponible
                 * subject_type: recirculation-ws
                 * properties: Guardar en formato json tanto el XML de entrada como el de respuesta.
                 */
                $activity = activity('Recirculaciones')
                    ->withProperties([
                        'parameters' => $xml_entry,
                        'response' => null,
                    ])
                    ->log('Servicio no disponible');
                $activity->description = 'Servicio no disponible';
                $activity->subject_type = 'recirculation-ws';
                $activity->save();

                /**
                 * Lanzar excepción con mensaje “El servicio no se encuentra disponible en este momento” y el código de estado es 400 (Bad Request)
                 */
                throw new BadRequestException("El servicio no ha devuelto respuesta.");
            }

            return current($array);

        } catch (Exception $e) {

            /**
             * Otro de los casos de error que puede darse es “Connection timeout“, lo cual suele ocurrir entre los 6 y 10 seg. de demora. En este punto se debe realizar las siguientes acciones:
             * Guardar log en tabla “activity_log“. La información de los campos de esta será:
             * log_name: Recirculaciones
             * log_description: Servicio no disponible
             * subject_type: recirculation-ws
             * properties: Guardar en formato json tanto el XML de entrada como el de respuesta.
             */
            $activity = activity('Recirculaciones')
                    ->withProperties([
                        'parameters' => $xml_entry,
                        'response' => json_encode($e),
                    ])
                    ->log('Servicio no disponible');
            $activity->description = 'Servicio no disponible';
            $activity->subject_type = 'recirculation-ws';
            $activity->save();

            /**
             * Lanzar excepción con mensaje “El servicio no se encuentra disponible en este momento” y el código de estado es 400 (Bad Request)
             */
            throw new BadRequestException("Recirculaciones: El servicio no se encuentra disponible en este momento.", ['error_detail' => $e->getMessage()]);
        }

    }

}
