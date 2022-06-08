<?php

namespace App\Services\External\FreightVerify;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class FreightVerifyService {

  /**
   * @var \GuzzleHttp\Client Cliente para realizar peticiones HTTP
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

  public function __construct() {

    $serviceConfig = Config::get('services.freightVerify');

    $this->client = new \GuzzleHttp\Client([
      'base_uri' => $serviceConfig['url'],
      'http_errors' => false,
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'auth' => [
        $serviceConfig['user'],
        $serviceConfig['pass']
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
   * @throws \Exception
   */
  public function updateVinMilestone(string $vin, FreightVerifyMilestone $milestone ,array $references=[], $retries=3): array {

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
    $bodyRefs = collect($references)->keys()->map(function($key) use ($references) {
      return ['qualifier' => [$key => $references[$key]]];
    });

    // Intentamos 3 hasta veces la request en caso de fallo
    for ($i=0; $i < $retries; $i++) {

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
      $this->logActivity($milestone, $body, $response);
      // Si devuelve cualquier status code que no indique una petición correcta, reintentamos
      if ($this->isValidStatusCode($status)) {
        continue;
      }
      // Si no necesitamos reintento, salimos del bucle
      break;
    }

    if (!$this->isValidStatusCode($status)) {
      throw new \Exception("Error al conectar con el servicio externo: FreightVerify", Response::HTTP_INTERNAL_SERVER_ERROR);
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
   * @throws \Exception
   */
  public function sendVehicleReceived(string $vin, array $references, $retries=3): array {
    $milestone = FreightVerifyMilestone::VEHICLE_RECEIVED;
    return $this->updateVinMilestone($vin, $milestone, $references, $retries);
  }

  /**
   * Envía el milestone de INSPECTION_COMPLETE a la API FreightVerify
   *
   * @param string $vin VIN del vehículo
   * @param array $references Array asociativo con los valores necesarios para la API
   * @param int $retries Número de intentos en caso de error
   * @return array Array asociativo con la respuesta de la API
   * @throws \Exception
   */
  public function sendInspectionCompleted(string $vin, array $references, $retries=3): array {
    $milestone = FreightVerifyMilestone::INSPECTION_COMPLETE;
    return $this->updateVinMilestone($vin, $milestone, $references, $retries);
  }

  /**
   * Envía el milestone de RELEASED_TO_CARRIER a la API FreightVerify
   *
   * @param string $vin VIN del vehículo
   * @param array $references Array asociativo con los valores necesarios para la API
   * @param int $retries Número de intentos en caso de error
   * @return array Array asociativo con la respuesta de la API
   * @throws \Exception
   */
  public function sendReleasedToCarrier(string $vin, array $references, $retries=3): array {
    $milestone = FreightVerifyMilestone::RELEASED_TO_CARRIER;
    return $this->updateVinMilestone($vin, $milestone, $references, $retries);
  }

  /**
   * Envía el milestone de COMPOUND_EXIT a la API FreightVerify
   *
   * @param string $vin VIN del vehículo
   * @param array $references Array asociativo con los valores necesarios para la API
   * @param int $retries Número de intentos en caso de error
   * @return array Array asociativo con la respuesta de la API
   * @throws \Exception
   */
  public function sendCompoundExit(string $vin, array $references, $retries=3): array {
    $milestone = FreightVerifyMilestone::COMPOUND_EXIT;
    return $this->updateVinMilestone($vin, $milestone, $references, $retries);
  }

  /**
   * @param \App\Services\External\FreightVerify\FreightVerifyMilestone $milestone Milestone efectuado.
   * @param array $body Array que contiene los datos enviados a la API.
   * @param \GuzzleHttp\Psr7\Response $response Respuesta de la API.
   * @return void
   */
  private function logActivity(FreightVerifyMilestone $milestone, array $body, GuzzleResponse $response) {
    $status = $response->getStatusCode();
    $activity = activity('FreightVerify-'.$milestone->name)
      ->withProperties([
        'parameters' => $body,
        'response' => json_decode($response->getBody()),
        'status_code' => $status
      ])
      ->log($this->isValidStatusCode($status) ? 'Exito en el servicio' : 'Error en el servicio');
    $activity->subject_type = 'FreightVerify-'.$milestone->name.'-ws';
    $activity->save();
    return $activity;
  }

  /**
   * @param int $status Código de estado HTTP
   * @return bool Devuelve si es un código de éxito o no (entre 200 y 300)
   */
  private function isValidStatusCode(int $status) {
    return $status >= Response::HTTP_OK && $status < Response::HTTP_MULTIPLE_CHOICES;
  }

  /**
   * @param array $extraRules Reglas extra de validacion Laravel
   * @return array Array de reglas de validación de Laravel. Las base del servicio, más las extra recibidas por parámetro
   */
  public static function getValidationRules($extraRules=[]) {
    return array_merge(self::$validationRules, $extraRules);
  }
}