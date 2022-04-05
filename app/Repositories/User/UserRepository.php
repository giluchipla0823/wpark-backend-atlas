<?php

namespace App\Repositories\User;

use App\Helpers\QueryParamsHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Obtener listado de usuarios.
     *
     * @param Request $request
     * @return Collection
     */
    public function all(Request $request): Collection
    {
        $query = $this->model->query();

        $query->with(QueryParamsHelper::getIncludesParamFromRequest());

        if (QueryParamsHelper::checkIncludeParamDatatables()) {
            $result = Datatables::customizable($query)->response();

            return collect($result);
        }

        return $query->get();
    }

    // TODO: Añadir relaciones many to many con campas y dispositivos como en holds
    /**
     * Crear usuario.
     *
     * @param array $params
     * @return Model
     */
    public function create(array $params): Model
    {
        $params['password'] = bcrypt($params['password']);

        return $this->model->create($params);
    }

    /**
     * Actualizar usuario.
     *
     * @param array $params
     * @param int $id
     * @return int|null
     */
    public function update(array $params, int $id): ?int
    {
        if(array_key_exists('password', $params)){
            $params['password'] = bcrypt($params['password']);
        }

        return $this->model->where('id', $id)->update($params);
    }

    /**
     * Obtener usuario por el campo "username".
     *
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->model->where('username', $username)->first();
    }

}
