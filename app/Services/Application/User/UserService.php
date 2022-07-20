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

        return UserResource::collection($results)->collection;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function datatables(Request $request): Collection
    {
        $results = $this->repository->datatables($request);

        $results['data'] = UserResource::collection($results['data'])->collection;;

        return collect($results);
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
        $surname = strtoupper($surname);
        $searching = true;
        $letters = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
        $username = null;
        $usernameList = [
            $name[0],
            $surname[0]
        ];

        while ($searching) {
            shuffle($letters);

            $randLetters = array_rand($letters, 3);

            foreach ($randLetters as $val) {
                $usernameList[] = $letters[$val];
            }

            sort($usernameList);

            $username = implode("", $usernameList);

            if (!$this->repository->findOneByUsername($username)) {
                $searching = false;
            }
        }

        return $username;
    }

}
