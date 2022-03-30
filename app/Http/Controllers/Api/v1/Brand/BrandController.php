<?php

namespace App\Http\Controllers\Api\v1\Brand;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Brand\BrandService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Brand\BrandStoreRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;

class BrandController extends ApiController
{
    /**
     * @var BrandService
     */
    private $brandService;

    public function __construct(
        BrandService $brandService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->brandService = $brandService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/brands",
     *      tags={"Brands"},
     *      summary="Brands List",
     *      description="List of brands",
     *      security={{"sanctum": {}}},
     *      operationId="indexBrands",
     *      @OA\Response(response=200, description="Brand list Successfully"),
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
        $results = $this->brandService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/brands",
     *     tags={"Brands"},
     *     summary="Create New Brand",
     *     description="Create New Brand",
     *     security={{"sanctum": {} }},
     *     operationId="storeBrand",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BrandStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Brand" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param BrandStoreRequest $request
     * @return JsonResponse
     */
    public function store(BrandStoreRequest $request): JsonResponse
    {
        $brand = $this->brandService->create($request->all());

        return $this->successResponse($brand, 'Brand created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/brands/{id}",
     *     tags={"Brands"},
     *     summary="Show Brand Details",
     *     description="Show Brand Details",
     *     security={{"sanctum": {}}},
     *     operationId="showBrand",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Brand Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function show(Brand $brand): JsonResponse
    {
        $brand = $this->brandService->show($brand);
        return $this->successResponse($brand);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/brands/{id}",
     *     tags={"Brands"},
     *     summary="Update Brand",
     *     description="Update Brand",
     *     security={{"sanctum": {}}},
     *     operationId="updateBrand",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BrandUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Brand" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param BrandUpdateRequest $request
     * @param Brand $brand
     * @return JsonResponse
     */
    public function update(BrandUpdateRequest $request, Brand $brand): JsonResponse
    {
        $this->brandService->update($request->all(), $brand->id);

        return $this->showMessage('Brand updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/brands/{id}",
     *     tags={"Brands"},
     *     summary="Delete Brand",
     *     description="Delete Brand",
     *     security={{"sanctum": {}}},
     *     operationId="destroyBrand",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Brand successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand): JsonResponse
    {
        $this->brandService->delete($brand->id);

        return $this->showMessage('Brand removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/brands/{id}",
     *     tags={"Brands"},
     *     summary="Restore Brand",
     *     description="Restore Brand",
     *     security={{"sanctum": {}}},
     *     operationId="restoreBrand",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Brand restored successfully"),
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
        $this->brandService->restore($id);

        return $this->showMessage('Brand restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
