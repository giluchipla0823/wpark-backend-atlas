<?php

namespace App\Models;

use Spatie\Permission\Models\Role as RoleModel;

/**
 *
 * @OA\Schema(
 * required={"name", "guard_name"},
 * @OA\Xml(name="Role"),
 * @OA\Property(property="id", type="integer", maxLength=20, readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", maxLength=255, description="Nombre del rol", example="admin"),
 * @OA\Property(property="guard_name", type="string", maxLength=255, description="Donde se aplica el rol", example="web"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Fecha y hora de la creación", example="2021-09-07 09:41:35"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha y hora de la última modificación", example="2021-09-09 11:20:01")
 * )
 *
 * Class Role
 *
 */

class Role extends RoleModel
{
    public function holds()
    {
        return $this->hasMany(Hold::class, 'role_id');
    }
}
