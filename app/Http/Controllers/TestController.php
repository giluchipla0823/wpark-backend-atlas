<?php

namespace App\Http\Controllers;

use App\Events\CompletedRowNotification;
use App\Models\Movement;
use Illuminate\Http\Request;
use App\Services\Application\Notification\NotificationService;
use App\Http\Controllers\ApiController;
use App\Models\Row;
use App\Models\User;
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
        $row = Row::find(2);
        $sender = User::find(2);

        event(new CompletedRowNotification($sender, $row));

        return $this->showMessage("Notification sent");
    }

}
