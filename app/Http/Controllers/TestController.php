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

    public function script() {
        $array = ['N2','N3','N4','N5','N7','TU','TK','UV','UW','1F','01','02','03','04','05','06','07','08','09','0H','10','11','12','13','14','15','20','21','22','23','2I','26','27','29','32','33','34','35','36','37','38','39','44','50','56','66','85','89','8B','A3','A8','CE','CF','CK','CU','EW','90','91','92','93','94','95','97','98','99','AA','AB','AE','AF','AK','AL','AM','AP','AQ','AT','AZ','ER','Y1','C6','C7','C8','C9','CY','1R','2R','CZ','D5','D6','1A','VS','NS', 'NT', 'NU', 'NV', 'NW','MP', 'MQ', 'MS', 'MT', 'MU', 'MV', 'MX', 'PO', 'QD', 'QB'];

        $sql = "";


        foreach ($array as $value) {
            $sql .= "INSERT INTO `rules_conditions` (`rule_id`, `condition_id`, `conditionable_type`, `conditionable_id`, `created_at`, `updated_at`)";
            // $sql .= "\n";
            $sql .= "<br>";
            $sql .= "VALUES ((SELECT `id` FROM rules WHERE `name` = 'ANTWERP' LIMIT 1), 5, 'App\\\\Models\\\\DestinationCode', (SELECT id FROM destination_codes WHERE `code` = '{$value}' LIMIT 1), NOW(), NOW());";
            // $sql .= "\n\n";
            $sql .= "<br><br>";
        }


        echo $sql;





    }

}
