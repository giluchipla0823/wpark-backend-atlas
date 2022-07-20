<?php

namespace App\Http\Controllers\External\FORD;

use Exception;
use App\Models\Load;
use App\Models\Route;
use App\Models\Vehicle;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use App\Helpers\FordSt8ApiHelper;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\FORD\TransportST8Request;
use App\Services\External\FORD\TransportST8Service;
use App\Exceptions\FORD\FordStandardErrorException;

class TransportST8Controller extends ApiController
{
    /**
     * @var TransportST8Service
     */
    private $transportST8Service;

    public function __construct(
        TransportST8Service $transportST8Service
    ) {
        $this->transportST8Service = $transportST8Service;
    }

    /**
     * Provision a new web server.
     *
     * @param TransportST8Request $request
     * @return void
     * @throws Exception
     * @throws GuzzleException
     */
    public function __invoke(TransportST8Request $request): JsonResponse
    {
        $transportId = $request->get("id");
        $transportType = $request->get("type");
        $transportContent = $request->get("transportContent");
        $transportContentCollection = collect($transportContent);

        $cdmCodes = $transportContentCollection->pluck('cdmCode')->unique()->toArray();
        $vins = $transportContentCollection->pluck('vehicles.*.vin')
                    ->flatMap(function($item) { return $item; })
                    ->values()
                    ->toArray();

        // Obtener el Load con el id de transporte que se ha enviado
        $load = Load::where('transport_identifier', $transportId)->first();

        if (!$load) {
            throw new FordStandardErrorException([
                "Load with transport_identifier: $transportId doesn't not exists"
            ], Response::HTTP_NOT_FOUND);
        }

        if(!in_array($transportType, FordSt8ApiHelper::getAllowedTransportType())){
            throw new FordStandardErrorException([
                "Type transport: $transportType doesn't not exists"
            ], Response::HTTP_NOT_FOUND);
        }

        $notFoundErrorMessages = [];

        $routes = Route::whereIn('cdm_code', $cdmCodes)->get();
        $vehicles = Vehicle::whereIn('vin', $vins)->get();

        foreach ($transportContent as $transport) {
            $cdmCode = $transport['cdmCode'];

            foreach ($transport['vehicles'] as $vehicleItem) {
                $vin = $vehicleItem['vin'];

                $vehicle = $vehicles->where('vin', $vin)->first();

                if (!$vehicle) {
                    $notFoundErrorMessages[] = "Vehicle with vin '$vin' doesn't not exists";
                    continue;
                }

                if (!$vehicle->loads || $vehicle->loads->id !== $load->id) {
                    $notFoundErrorMessages[] = "Vehicle with vin '$vin' doesn't not exists in load with transport_identifier: $transportId";
                }

                if (!$vehicle->route) {
                    $notFoundErrorMessages[] = "Vehicle with vin '$vin' does not have a cdm_code assigned to it.";
                }

                if ($vehicle->route && $vehicle->route->cdm_code !== $cdmCode) {
                    $notFoundErrorMessages[] = "Vehicle with vin '$vin' has an assigned cdm_code that is different from cmd_code '$cdmCode'";
                }
            }
        }

        foreach ($cdmCodes as $cdm_code) {
            if($routes->where('cdm_code', $cdm_code)->isEmpty()){
                $notFoundErrorMessages[] = "Route with cdm_code '$cdm_code' doesn't not exists";
            }
        }

        if(!empty($notFoundErrorMessages)){
            throw new FordStandardErrorException($notFoundErrorMessages, Response::HTTP_NOT_FOUND);
        }

        $transportType = FordSt8ApiHelper::getTransportType($load->exit_transport_id);

        $transportContent = array_map(function($transport) {
            $vehicles = $transport["vehicles"];

            return [
                "cdmCode" => $transport["cdmCode"],
                "vehicles" => array_map(function($vehicle) {
                    return [
                        "vin" => $vehicle["vin"],
                        "imported" => (boolean) $vehicle["imported"]
                    ];
                }, $vehicles)
            ];
        }, $transportContent);

        $params = [
            "id" => $load->transport_identifier,
            "type" => $transportType,
            "transportContent" => $transportContent
        ];

        $this->transportST8Service->connection($params);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
