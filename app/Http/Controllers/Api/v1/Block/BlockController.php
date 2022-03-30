<?php

namespace App\Http\Controllers\Api\v1\Block;

use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Block\BlockService;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Block\BlockStoreRequest;
use App\Http\Requests\Block\BlockUpdateRequest;

class BlockController extends ApiController
{
    /**
     * @var BlockService
     */
    private $blockService;

    public function __construct(
        BlockService $blockService
    )
    {
        $this->middleware('role:Super-Admin|admin');
        $this->blockService = $blockService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/blocks",
     *      tags={"Blocks"},
     *      summary="Blocks List",
     *      description="List of blocks",
     *      security={{"sanctum": {}}},
     *      operationId="indexBlocks",
     *      @OA\Response(response=200, description="Block list Successfully"),
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
        $results = $this->blockService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/blocks",
     *     tags={"Blocks"},
     *     summary="Create New Block",
     *     description="Create New Block",
     *     security={{"sanctum": {} }},
     *     operationId="storeBlock",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BlockStoreRequest")
     *     ),
     *     @OA\Response(response=201, description="Create New Block" ),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param BlockStoreRequest $request
     * @return JsonResponse
     */
    public function store(BlockStoreRequest $request): JsonResponse
    {
        $block = $this->blockService->create($request->all());

        return $this->successResponse($block, 'Block created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/blocks/{id}",
     *     tags={"Blocks"},
     *     summary="Show Block Details",
     *     description="Show Block Details",
     *     security={{"sanctum": {}}},
     *     operationId="showBlock",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show Block Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param Block $block
     * @return JsonResponse
     */
    public function show(Block $block): JsonResponse
    {
        $block = $this->blockService->show($block);
        return $this->successResponse($block);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/blocks/{id}",
     *     tags={"Blocks"},
     *     summary="Update Block",
     *     description="Update Block",
     *     security={{"sanctum": {}}},
     *     operationId="updateBlock",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BlockUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update Block" ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param BlockUpdateRequest $request
     * @param Block $block
     * @return JsonResponse
     */
    public function update(BlockUpdateRequest $request, Block $block): JsonResponse
    {
        $this->blockService->update($request->all(), $block->id);

        return $this->showMessage('Block updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/blocks/{id}",
     *     tags={"Blocks"},
     *     summary="Delete Block",
     *     description="Delete Block",
     *     security={{"sanctum": {}}},
     *     operationId="destroyBlock",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete Block successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param Block $block
     * @return JsonResponse
     */
    public function destroy(Block $block): JsonResponse
    {
        $this->blockService->delete($block->id);

        return $this->showMessage('Block removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/blocks/{id}",
     *     tags={"Blocks"},
     *     summary="Restore Block",
     *     description="Restore Block",
     *     security={{"sanctum": {}}},
     *     operationId="restoreBlock",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Block restored successfully"),
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
        $this->blockService->restore($id);

        return $this->showMessage('Block restored successfully.', Response::HTTP_NO_CONTENT);
    }


}
