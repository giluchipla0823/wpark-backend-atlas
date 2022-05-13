<?php

namespace App\Http\Controllers\Api\v1\Color;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Color\ColorStoreRequest;
use App\Http\Requests\Color\ColorUpdateRequest;
use App\Models\Color;
use App\Services\Application\Color\ColorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends ApiController
{
    /**
     * @var ColorService
     */
    private $colorService;

    public function __construct(
        ColorService $colorService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->colorService = $colorService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/colors",
     *      tags={"Colors"},
     *      summary="Colors List",
     *      description="List of colors",
     *      security={{"sanctum": {}}},
     *      operationId="indexColors",
     *      @OA\Response(response=200, description="Color list Successfully"),
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
        $results = $this->colorService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/colors",
     *     tags={"Colors"},
     *     summary="Create New Color",
     *     description="Create New Color",
     *     security={{"sanctum": {} }},
     *     operationId="storeColor",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ColorStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Color" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param ColorStoreRequest $request
     * @return JsonResponse
     */
    public function store(ColorStoreRequest $request): JsonResponse
    {
        $color = $this->colorService->create($request->all());

        return $this->successResponse($color, 'Color created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/colors/{id}",
     *     tags={"Colors"},
     *     summary="Show Color Details",
     *     description="Show Color Details",
     *     security={{"sanctum": {}}},
     *     operationId="showColor",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Color Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Color $color
     * @return JsonResponse
     */
    public function show(Color $color): JsonResponse
    {
        $color = $this->colorService->show($color);
        return $this->successResponse($color);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/colors/{id}",
     *     tags={"Colors"},
     *     summary="Update Color",
     *     description="Update Color",
     *     security={{"sanctum": {}}},
     *     operationId="updateColor",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/ColorUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Color" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param ColorUpdateRequest $request
     * @param Color $color
     * @return JsonResponse
     */
    public function update(ColorUpdateRequest $request, Color $color): JsonResponse
    {
        $this->colorService->update($request->all(), $color->id);

        return $this->showMessage('Color updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/colors/{id}",
     *     tags={"Colors"},
     *     summary="Delete Color",
     *     description="Delete Color",
     *     security={{"sanctum": {}}},
     *     operationId="destroyColor",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Color successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Color $color
     * @return JsonResponse
     */
    public function destroy(Color $color): JsonResponse
    {
        $this->colorService->delete($color->id);

        return $this->showMessage('Color removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/colors/{id}",
     *     tags={"Colors"},
     *     summary="Restore Color",
     *     description="Restore Color",
     *     security={{"sanctum": {}}},
     *     operationId="restoreColor",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Color restored successfully"),
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
        $this->colorService->restore($id);

        return $this->showMessage('Color restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
