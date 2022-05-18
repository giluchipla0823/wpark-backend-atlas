<?php

namespace App\Http\Controllers\Api\v1\Notification;

use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Services\Application\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends ApiController
{
    /**
     * @var NotificationService
     */
    private $notificationService;

    public function __construct(
        NotificationService $notificationService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->notificationService = $notificationService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/notifications",
     *      tags={"Notifications"},
     *      summary="Notifications List",
     *      description="List of notifications",
     *      security={{"sanctum": {}}},
     *      operationId="indexNotifications",
     *      @OA\Parameter(
     *         name="reference_code",
     *         in="query",
     *         description="Filtrar por reference_code",
     *         example="ROW_COMPLETED",
     *         required=false
     *      ),
     *      @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Límite de resultados",
     *         example="5",
     *         required=false
     *      ),
     *      @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Ordenar por campo específico",
     *         example="id",
     *         required=false
     *      ),
     *      @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Ordenar por orden ascendente o descendente",
     *         example="desc",
     *         required=false
     *      ),
     *      @OA\Response(response=200, description="Notification list Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $results = $this->notificationService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/notifications/datatables",
     *      tags={"Notifications"},
     *      summary="Notifications List",
     *      description="List of notifications with datatables",
     *      security={{"sanctum": {}}},
     *      operationId="datatablesNotifications",
     *      @OA\Parameter(ref="#/components/parameters/datatables"),
     *      @OA\Response(response=200, description="Notifications list Successfully"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatables(Request $request): JsonResponse
    {
        $request->query->add(['datatables' => 1]);

        $results = $this->notificationService->datatables($request);

        return $this->showAll($results);
    }

    // /**
    // * @OA\POST(
    // *     path="/api/v1/notifications",
    // *     tags={"Notifications"},
    // *     summary="Create New Notification",
    // *     description="Create New Notification",
    // *     security={{"sanctum": {} }},
    // *     operationId="storeNotification",
    // *     @OA\RequestBody(
    // *          required=true,
    // *          @OA\JsonContent(ref="#/components/schemas/NotificationStoreRequest")
    // *     ),
    // *     @OA\Response(response=201, description="Create New Notification" ),
    // *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
    // *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
    // *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
    // *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
    // * )
    // *
    // * Store a newly created resource in storage.
    // *
    // * @param NotificationStoreRequest $request
    // * @return JsonResponse
    // */
    /* public function store(Request $request): JsonResponse
    {
        $notification = $this->notificationService->create($request->all());

        return $this->successResponse($notification, 'Notification created successfully.', Response::HTTP_CREATED);
    } */

    /**
     * @OA\GET(
     *     path="/api/v1/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Show Notification Details",
     *     description="Show Notification Details",
     *     security={{"sanctum": {}}},
     *     operationId="showNotification",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         description="Añadir bloques, condiciones",
     *         example="blocks,conditions",
     *         required=false
     *     ),
     *     @OA\Parameter(
     *         name="extra_includes",
     *         in="query",
     *         description="Añadir valores de condiciones",
     *         example="conditions.values",
     *         required=false
     *     ),
     *     @OA\Response(response=200, description="Show Notification Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Notification $notification
     * @return JsonResponse
     */
    public function show(Notification $notification): JsonResponse
    {
        $notification = $this->notificationService->show($notification);
        return $this->successResponse($notification);
    }

    ///**
    // * @OA\PUT(
    // *     path="/api/v1/notifications/{id}",
    // *     tags={"Notifications"},
    // *     summary="Update Notification",
    // *     description="Update Notification",
    // *     security={{"sanctum": {}}},
    // *     operationId="updateNotification",
    // *     @OA\Parameter(ref="#/components/parameters/id"),
    // *     @OA\RequestBody(
    // *          required=true,
    // *          @OA\JsonContent(ref="#/components/schemas/NotificationUpdateRequest")
    // *     ),
    // *     @OA\Response(response=200, description="Update Notification" ),
    // *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
    // *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
    // *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
    // *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
    // *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
    // * )
    // *
    // * Update the specified resource in storage.
    // *
    // * @param Request $request
    // * @param Notification $notification
    // * @return JsonResponse
    // */
    /* public function update(Request $request, Notification $notification): JsonResponse
    {
        $this->notificationService->update($request->all(), $notification->id);

        return $this->showMessage('Notification updated successfully.');
    } */

    /**
     * @OA\Delete(
     *     path="/api/v1/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Delete Notification",
     *     description="Delete Notification",
     *     security={{"sanctum": {}}},
     *     operationId="destroyNotification",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Notification successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Notification $notification
     * @return JsonResponse
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $this->notificationService->delete($notification->id);

        return $this->showMessage('Notification removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Restore Notification",
     *     description="Restore Notification",
     *     security={{"sanctum": {}}},
     *     operationId="restoreNotification",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Notification restored successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Restores the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $this->notificationService->restore($id);

        return $this->showMessage('Notification restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
