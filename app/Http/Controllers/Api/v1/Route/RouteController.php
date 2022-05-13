<?php

namespace App\Http\Controllers\Api\v1\Route;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Route\RouteStoreRequest;
use App\Http\Requests\Route\RouteUpdateRequest;
use App\Models\Route;
use App\Services\Application\Route\RouteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RouteController extends ApiController
{
    /**
     * @var RouteService
     */
    private $routeService;

    public function __construct(
        RouteService $routeService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->routeService = $routeService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/routes",
     *      tags={"Routes"},
     *      summary="Routes List",
     *      description="List of routes",
     *      security={{"sanctum": {}}},
     *      operationId="indexRoutes",
     *      @OA\Response(response=200, description="Route list Successfully"),
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
        $results = $this->routeService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/routes",
     *     tags={"Routes"},
     *     summary="Create New Route",
     *     description="Create New Route",
     *     security={{"sanctum": {} }},
     *     operationId="storeRoute",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RouteStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Route" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param RouteStoreRequest $request
     * @return JsonResponse
     */
    public function store(RouteStoreRequest $request): JsonResponse
    {
        $route = $this->routeService->create($request->all());

        return $this->successResponse($route, 'Route created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/routes/{id}",
     *     tags={"Routes"},
     *     summary="Show Route Details",
     *     description="Show Route Details",
     *     security={{"sanctum": {}}},
     *     operationId="showRoute",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Route Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Route $route
     * @return JsonResponse
     */
    public function show(Route $route): JsonResponse
    {
        $route = $this->routeService->show($route);
        return $this->successResponse($route);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/routes/{id}",
     *     tags={"Routes"},
     *     summary="Update Route",
     *     description="Update Route",
     *     security={{"sanctum": {}}},
     *     operationId="updateRoute",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RouteUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Route" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param RouteUpdateRequest $request
     * @param Route $route
     * @return JsonResponse
     */
    public function update(RouteUpdateRequest $request, Route $route): JsonResponse
    {
        $this->routeService->update($request->all(), $route->id);

        return $this->showMessage('Route updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/routes/{id}",
     *     tags={"Routes"},
     *     summary="Delete Route",
     *     description="Delete Route",
     *     security={{"sanctum": {}}},
     *     operationId="destroyRoute",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Route successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Route $route
     * @return JsonResponse
     */
    public function destroy(Route $route): JsonResponse
    {
        $this->routeService->delete($route->id);

        return $this->showMessage('Route removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/routes/{id}",
     *     tags={"Routes"},
     *     summary="Restore Route",
     *     description="Restore Route",
     *     security={{"sanctum": {}}},
     *     operationId="restoreRoute",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Route restored successfully"),
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
        $this->routeService->restore($id);

        return $this->showMessage('Route restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
