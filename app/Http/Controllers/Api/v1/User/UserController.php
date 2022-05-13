<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\ApiController;
use App\Http\Requests\User\UserGenerateUsernameRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\Application\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ApiController
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->middleware('role:Super-Admin|admin')->except('me');
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Users List",
     *      description="List of users",
     *      security={ {"sanctum": {} }},
     *      operationId="indexUsers",
     *      @OA\Response(response=200, description="User list Successfully"),
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
        $results = $this->userService->all($request);

        return $this->showAll($results);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Create New User",
     *     description="Create New User",
     *     security={ {"sanctum": {} }},
     *     operationId="storeUser",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserStoreRequest")
     *     ),
     *      @OA\Response(response=201, description="Create New User" ),
     *      @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *      @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *      @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *      @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->all());

        return $this->successResponse($user, 'User created successfully.', Response::HTTP_CREATED);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Show User Details",
     *     description="Show User Details",
     *     security={{"sanctum": {}}},
     *     operationId="showUser",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=200, description="Show User Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user = $this->userService->show($user);
        return $this->successResponse($user);
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Update User",
     *     description="Update User",
     *     security={{"sanctum": {}}},
     *     operationId="updateUser",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
     *     ),
     *     @OA\Response(response=200, description="Update User successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntity"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($request->all(), $user->id);

        return $this->successResponse($user, 'User updated successfully.');
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Delete User",
     *     description="Delete User",
     *     security={{"sanctum": {}}},
     *     operationId="destroyUser",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="Delete User successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user->id);

        return $this->showMessage('User removed successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\PATCH(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Restore User",
     *     description="Restore User",
     *     security={{"sanctum": {}}},
     *     operationId="restoreUser",
     *     @OA\Parameter(ref="#/components/parameters/id"),
     *     @OA\Response(response=204, description="User restored successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     * Enable the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $this->userService->restore($id);

        return $this->showMessage('User restored successfully.', Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/me",
     *     tags={"Users"},
     *     summary="Show Login User Details",
     *     description="Show Login User Details",
     *     security={{"sanctum": {}}},
     *     operationId="meUser",
     *     @OA\Response(response=200, description="Show Login User Details"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->userService->me($request->user());
        return $this->successResponse($user);
    }

    /**
     * @OA\POST(
     *     path="/api/v1/users/generate-username",
     *     tags={"Users"},
     *     summary="Generate Username",
     *     description="Generate Username",
     *     security={{"sanctum": {}}},
     *     operationId="generateUsernameUser",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserGenerateUsernameRequest")
     *     ),
     *     @OA\Response(response=200, description="Get username value"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=500, ref="#/components/responses/InternalServerError")
     * )
     *
     * @param UserGenerateUsernameRequest $request
     * @return JsonResponse
     */
    public function generateUsername(UserGenerateUsernameRequest $request): JsonResponse
    {
        $username = $this->userService->generateUsername(
            $request->get('name'),
            $request->get('surname')
        );

        return $this->successResponse(['username' => $username]);
    }
}
