<?php

namespace App\Services\External\FORD;

use App\Models\ActivityLog;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\FORD\FordStandardErrorException;

class TransportST8Service
{
    /**
     * @var string
     */
    protected $stationName;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        /**
         * TODO: Revisar tema de URL en API ST8
         * URL 01: http://vacdm.valencia.ford.com:8080/stations/station8/transports
         * URL 02: http://vacdm.valencia.ford.com:8080/stations/station8/transports/4Tcg07YyEx/Truck
         *
         */
        $this->baseUrl = config("services.ford_services.st8.url");
        $this->stationName = "station8";

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => true,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'verify' => false
        ]);
    }

    /**
     * @param array $params
     * @return array
     * @throws FordStandardErrorException
     * @throws GuzzleException
     */
    public function connection(array $params): array
    {
        $transportType = $params["type"];
        $transportIdentifier = $params["id"];

        // $url = "/stations/{$this->stationName}/transports/{$transportIdentifier}/{$transportType}";
        $url = "/stations/{$this->stationName}/transports";

        try {
            $request = $this->client->post($url, $params);

            $response = json_decode($request->getBody()->getContents(), true);

            $this->saveLog('Éxito en el servicio', $params, $response);

            return $response;
        } catch (Exception $exc) {
            // dd($exc->getRequest()->getUri());
            // $response = json_decode($exc->getResponse()->getBody()->getContents(), true);

            if ($exc instanceof ConnectException) {
                $handlerContext = $exc->getHandlerContext();

                $this->saveLog('Error en el servicio', $params, ['error' => $handlerContext['error']]);

                throw new FordStandardErrorException(["Service Error: {$handlerContext['error']}"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $this->saveLog('Error en el servicio', $params);

            throw new FordStandardErrorException(["Service Error | Status Code: {$exc->getCode()}"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @param string $message
     * @param array $parameters
     * @param array $response
     * @return void
     */
    private function saveLog(string $message, array $parameters, array $response = []): void
    {
        $activity = activity('FORD - Transport ST8')
            ->withProperties([
                "parameters" => json_encode($parameters),
                "response" => json_encode($response),
            ])
            ->log($message);
        $activity->reference_code = ActivityLog::REFERENCE_CODE_ST8;
        $activity->save();
    }
}
