<?php

namespace App\Http\Controllers;

use App\Events\CompletedRowNotification;
use App\Models\Load;
use App\Models\Movement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Services\Application\Notification\NotificationService;
use App\Http\Controllers\ApiController;
use App\Models\Row;
use App\Models\User;
use Milon\Barcode\DNS1D;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController extends ApiController
{
    /**
     * @var NotificationService
     */
    private $notificationService;

    public function __construct(
        NotificationService $notificationService
    )
    {
        $this->notificationService = $notificationService;
    }

    public function test(Request $request): JsonResponse{
        $movement = Movement::find(1);

        dd($movement);


        $notification = $this->notificationService->create($request->all());
        dd($notification);
        return $this->successResponse($notification, 'Notification created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendRowNotification(Request $request): JsonResponse
    {
        $row = Row::find(1);
        $sender = User::find(2);

        event(new CompletedRowNotification($sender, $row));

        return $this->showMessage("Notification sent");
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function domPdf() {
        $load = Load::find(1);

        $vehicles = $load->vehicles;

        $totalWeight = array_sum($vehicles->pluck('design.weight')->toArray());

        $data = [
            'load' => $load,
            'vehicles' => $vehicles,
            'counter_vehicles' => count($vehicles),
            'total_weight' => $totalWeight
        ];

        $pdf = Pdf::loadView('pdf.loads.albaran-transport', $data);

        return $pdf->download("albaran-transport-{$load->transport_identifier}.pdf");
    }

}
