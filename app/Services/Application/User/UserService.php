<?php

namespace App\Services\Application\User;

use App\Helpers\QueryParamsHelper;
use App\Http\Resources\User\MeResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $results = $this->repository->all($request);

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $data = collect($results->get('data'));

            $resource = UserResource::collection($data);

            $results->put('data', $resource->toArray($request));

            return $results;
        }

        return UserResource::collection($results)->collection;
    }

    /**
     * @param User $user
     * @return MeResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * @param array $params
     * @return User
     */
    public function create(array $params): User
    {
        return $this->repository->create($params);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * @param array $params
     * @param int $id
     * @return int|null
     */
    public function update(array $params, int $id): ?int
    {
        return $this->repository->update($params, $id);
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function restore(int $id): ?bool
    {
        return $this->repository->restore($id);
    }

    /**
     * @param User $user
     * @return MeResource
     */
    public function me(User $user): MeResource
    {
        return new MeResource($user);
    }

    /**
     * Generar "username" para un usuario.
     *
     * @param string $name
     * @param string $surname
     * @return string
     */
    public function generateUsername(string $name, string $surname): string
    {
        $name = strtoupper($name);
        $surname = explode(' ', strtoupper($surname));
        $searching = true;
        $letters = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
        $exists = 0;

        $username = $name[0];

        foreach ($surname as $value) {
            $username.= $value[0];
        }

        while ($searching) {
            shuffle($letters);

            if ($exists >= 50) {
                $username = $letters[0] . $letters[1] . $letters[2] . $letters[3];
            }else if ($exists >= 9) {
                $username = $name[0] . $letters[0] . $letters[1] . $letters[2];
            } else {
                $username.= $letters[0];

                if(count($surname) === 1) {
                    $username.= $letters[0] . $letters[1];
                }
            }

            if ($this->repository->findOneByUsername($username)) {
                $exists++;
            } else {
                $searching = false;
            }
        }

        return $username;
    }

}
