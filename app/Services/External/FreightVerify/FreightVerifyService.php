<?php

namespace App\Services\External\FreightVerify;

use App\Models\Vehicle;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class FreightVerifyService
{

    /**
     * @var Client Cliente para realizar peticiones HTTP
     */
    private $client;

    /**
     * @var array Reglas de validación comunes
     *
     */
    private static $validationRules = [
        'vin' => 'required|exists:vehicles,vin',
        'senderName' => 'required|exists:users,username',
        'scac' => 'required',
        'ms1LocationCode' => 'required',
        'ms1StateOrProvinceCode' => 'required',
        'ms1CountryCode' => 'required',
        'compoundCode' => 'required',
        'yardCode' => 'required',
        'bayCode' => 'required',
        'nextCarrier' => 'required',
        'equipmentType' => 'required',
        'equipmentNumber' => 'required',
        'voyageNumber' => 'required',
        'assetId' => 'required',
    ];

    public function __construct()
    {
        $config = config('services.freight_verify');

        $this->client = new Client([
            'base_uri' => $config['url'],
            'http_errors' => false,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth' => [
                $config['user'],
                $config['password']
            ]
        ]);
    }

    /**
     * Función genérica para envío de milestones a la API FreightVerify
     *
     * @param string $vin VIN del vehículo
     * @param FreightVerifyMilestone $milestone Milestone a enviar por API
     * @param array $references Array asociativo con los valores necesarios para la API
     * @param int $retries Número de intentos en caso de error
     * @return array Array asociativo con la respuesta de la API
     * @throws GuzzleException
     * @throws Exception
     */
    public function updateVinMilestone(string $vin, FreightVerifyMilestone $milestone, array $references = [], int $retries = 3): array
    {

        $vehicle = Vehicle::where('vin', $vin)->first();

        // Insertamos el código del milestone y del receiver (comun a todas las requests)
        $references['vmacsCode'] = $milestone->getVmacsCode();
        $references['scac'] = 'GNAHA';
        $references['receiverCode'] = 'FORDIT';
        $references['vehicleReceiptLocation'] = 'POL';
        $references['partnerType'] = 'IYO';
        $references['senderName'] = 'TSI';
        $references['compoundCode'] = 'VALENCIA';
        $references['ms1LocationCode'] = 'VALENCIA';
        $references['ms1CountryCode'] = 'ES';

        // Mapeamos al formato de la request de FreightVerify
        $bodyRefs = collect($references)->keys()->map(function ($key) use ($references) {
            return ['qualifier' => [$key => $references[$key]]];
        });

        // Intentamos 3 hasta veces la request en caso de fallo
        for ($i = 0; $i < $retries; $i++) {

            $body = [
                'json' => [
                    'vin' => $vin,
                    'code' => $milestone->getCode(),
                    'statusUpdateTs' => Carbon::now()->toISOString(),
                    'references' => $bodyRefs
                ],
                'headers' => [
                    'X-WSS-correlationId' => Str::uuid()->toString()
                ]
            ];

            // Realizamos la petición HTTP
            $response = $this->client->post('/api_adapters/inbound/ford/vin_milestones', $body);

            $status = $response->getStatusCode();
            $this->logActivity($milestone, $body, $response, $vehicle);
            // Si devuelve cualquier status code que no indique una petición correcta, reintentamos
            if ($this->isValidStatusCode($status)) {
                continue;
            }
            // Si no necesitamos reintento, salimos del bucle
            break;
        }

        if (!$this->isValidStatusCode($status)) {
            throw new Exception("Error al conectar con el servicio externo: FreightVerify", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Envía el milestone de VEHICLE_RECEIVED a la API FreightVerify
     *
     * @param string $vin VIN del vehículo
     * @param array $references Array asociativo con los valores necesarios para la API
     * @param int $retries Número de intentos en caso de error
     * @return array Array asociativo con la respuesta de la API
     * @throws GuzzleException
     */
    public function sendVehicleReceived(string $vin, array $references, $retries = 3): array
    {
        $milestone = new FreightVerifyMilestone(FreightVerifyMilestone::VEHICLE_RECEIVED);

        return $this->updateVinMilestone($vin, $milestone, $references, $retries);
    }

    /**
     * Envía el milestone de INSPECTION_COMPLETE a la API FreightVerify
     *
     * @param string $vin VIN del vehículo
     * @param array $references Array asociativo con los valores necesarios para la API
     * @param int $retries Número de intentos en caso de error
     * @return array Array asociativo con la respuesta de la API
     * @throws GuzzleException
     * @throws Exception
     */
    public function sendInspectionCompleted(string $vin, array $references, int $retries = 3): array
    {
        $milestone = new FreightVerifyMilestone(FreightVerifyMilestone::INSPECTION_COMPLETE);

        return $this->updateVinMilestone($vin, $milestone, $references, $retries);
    }

    /**
     * Envía el milestone de RELEASED_TO_CARRIER a la API FreightVerify
     *
     * @param string $vin VIN del vehículo
     * @param array $references Array asociativo con los valores necesarios para la API
     * @param int $retries Número de intentos en caso de error
     * @return array Array asociativo con la respuesta de la API
     * @throws GuzzleException
     * @throws Exception
     */
    public function sendReleasedToCarrier(string $vin, array $references, int $retries = 3): array
    {
        $milestone = new FreightVerifyMilestone(FreightVerifyMilestone::RELEASED_TO_CARRIER);

        return $this->updateVinMilestone($vin, $milestone, $references, $retries);
    }

    /**
     * Envía el milestone de COMPOUND_EXIT a la API FreightVerify
     *
     * @param string $vin VIN del vehículo
     * @param array $references Array asociativo con los valores necesarios para la API
     * @param int $retries Número de intentos en caso de error
     * @return array Array asociativo con la respuesta de la API
     * @throws GuzzleException
     * @throws Exception
     */
    public function sendCompoundExit(string $vin, array $references, int $retries = 3): array
    {
        $milestone = new FreightVerifyMilestone(FreightVerifyMilestone::COMPOUND_EXIT);

        return $this->updateVinMilestone($vin, $milestone, $references, $retries);
    }

    /**
     * @param FreightVerifyMilestone $milestone Milestone efectuado.
     * @param array $body Array que contiene los datos enviados a la API.
     * @param GuzzleResponse $response Respuesta de la API.
     * @param Vehicle|null $vehicle
     * @return void
     */
    private function logActivity(FreightVerifyMilestone $milestone, array $body, GuzzleResponse $response, ?Vehicle $vehicle): void
    {
        $status = $response->getStatusCode();

        $activity = activity("FreightVerify {$milestone->getName()}")
            ->withProperties([
                'parameters' => $body,
                'response' => json_decode($response->getBody()),
                'status_code' => $status
            ])
            ->log($this->isValidStatusCode($status) ? 'Exito en el servicio' : 'Error en el servicio');

        if ($vehicle) {
            $activity->subject_type = get_class($vehicle);
            $activity->subject_id = $vehicle->id;
        }

        $activity->reference_code = str_replace(' ', '-', strtolower("freightVerify-{$milestone->getName()}-ws"));

        $activity->save();
    }
    /**
     * @param int $status
     * @return bool
     */
    private function isValidStatusCode(int $status): bool
    {
        return $status >= Response::HTTP_OK && $status < Response::HTTP_MULTIPLE_CHOICES;
    }

    /**
     * @param array $extraRules Reglas extra de validacion Laravel
     * @return array Array de reglas de validación de Laravel. Las base del servicio, más las extra recibidas por parámetro
     */
    public static function getValidationRules(array $extraRules = []): array
    {
        return array_merge(self::$validationRules, $extraRules);
    }
}
