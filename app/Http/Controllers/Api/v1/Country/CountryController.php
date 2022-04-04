<?php

namespace App\Http\Controllers\Api\v1\Country;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Country\CountryService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Country\CountryStoreRequest;
use App\Http\Requests\Country\CountryUpdateRequest;

class CountryController extends ApiController
{
    /**
     * @var CountryService
     */
    private $countryService;

    public function __construct(
        CountryService $countryService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->countryService = $countryService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/countries",
     *      tags={"Countries"},
     *      summary="Countries List",
     *      description="List of countries",
     *      security={{"sanctum": {}}},
     *      operationId="indexCountries",
     *      @OA\Response(response=200, description="Country list Successfully"),
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
        $results = $this->countryService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/countries",
     *     tags={"Countries"},
     *     summary="Create New Country",
     *     description="Create New Country",
     *     security={{"sanctum": {} }},
     *     operationId="storeCountry",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CountryStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Country" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param CountryStoreRequest $request
     * @return JsonResponse
     */
    public function store(CountryStoreRequest $request): JsonResponse
    {
        $country = $this->countryService->create($request->all());

        return $this->successResponse($country, 'Country created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/countries/{id}",
     *     tags={"Countries"},
     *     summary="Show Country Details",
     *     description="Show Country Details",
     *     security={{"sanctum": {}}},
     *     operationId="showCountry",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Country Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Country $country
     * @return JsonResponse
     */
    public function show(Country $country): JsonResponse
    {
        $country = $this->countryService->show($country);
        return $this->successResponse($country);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/countries/{id}",
     *     tags={"Countries"},
     *     summary="Update Country",
     *     description="Update Country",
     *     security={{"sanctum": {}}},
     *     operationId="updateCountry",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CountryUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Country" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param CountryUpdateRequest $request
     * @param Country $country
     * @return JsonResponse
     */
    public function update(CountryUpdateRequest $request, Country $country): JsonResponse
    {
        $this->countryService->update($request->all(), $country->id);

        return $this->showMessage('Country updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/countries/{id}",
     *     tags={"Countries"},
     *     summary="Delete Country",
     *     description="Delete Country",
     *     security={{"sanctum": {}}},
     *     operationId="destroyCountry",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Country successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Country $country
     * @return JsonResponse
     */
    public function destroy(Country $country): JsonResponse
    {
        $this->countryService->delete($country->id);

        return $this->showMessage('Country removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/countries/{id}",
     *     tags={"Countries"},
     *     summary="Restore Country",
     *     description="Restore Country",
     *     security={{"sanctum": {}}},
     *     operationId="restoreCountry",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Country restored successfully"),
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
        $this->countryService->restore($id);

        return $this->showMessage('Country restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
