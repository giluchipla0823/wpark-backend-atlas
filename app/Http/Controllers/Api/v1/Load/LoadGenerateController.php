<?php


namespace App\Http\Controllers\Api\v1\Load;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Load\LoadGenerateRequest;
use App\Services\Application\Load\LoadService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoadGenerateController extends ApiController
{
    /**
     * @var LoadService
     */
    private $loadService;

    public function __construct(
        LoadService $loadService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->loadService = $loadService;
    }


    /**
     * @OA\POST(
     *     path="/api/v1/load/generate",
     *     tags={"Loads"},
     *     summary="Generate New Load",
     *     description="Generate New Load",
     *     security={{"sanctum": {} }},
     *     operationId="generateLoad",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoadGenerateRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Load" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=400, ref="#/components/responses/BadRequest"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Generate Load with data
     *
     * @param LoadGenerateRequest $request
     * @return JsonResponse
     */
    public function generate(LoadGenerateRequest $request): JsonResponse
    {
        $load = $this->loadService->generate($request->all());

        return $this->successResponse($load, 'Load created successfully.', Response::HTTP_CREATED);
    }
}
