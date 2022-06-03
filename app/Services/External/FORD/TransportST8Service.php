<?php

namespace App\Services\External\FORD;

use Illuminate\Support\Facades\Http;

class TransportST8Service
{
    private $transportIdentifier;
    private $transportType;
    private $stationName = "station8";
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config("services.ford_services.st8.url");
    }

    /**
     * @param array $params
     * @return array
     */
    public function connection(array $params): array
    {

        $this->transportType = $params['type'];
        $this->transportIdentifier = $params['id'];

        $url = $this->baseUrl . "/stations/$this->stationName/transports";
        if ($this->transportIdentifier){
            $url.='/'.$this->transportIdentifier;
        }
        if($this->transportType){
            $url.='/'.$this->transportType;
        }

        $response = Http::post($url, $params);

        $jsonData = $response->json();

        activity("FORD - Transport ST8")
            ->withProperties([
                "parameters" => $params,
                "response" => $jsonData,
            ])
            ->event('Error en el servicio')
            ->log("ford-transport-st8-ws");

        return $jsonData;
    }
}
