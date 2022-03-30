<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Repositories\User\UserRepositoryInterface;

class PermissionSeeder extends Seeder
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions Example
        //Permission::create(['name' => 'edit articles']);
        //Permission::create(['name' => 'delete articles']);
        //Permission::create(['name' => 'publish articles']);
        //Permission::create(['name' => 'unpublish articles']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        $role2 = Role::create(['name' => 'admin']);
        //$role2->givePermissionTo('edit articles');
        //$role2->givePermissionTo('delete articles');

        $role3 = Role::create(['name' => 'staff']);
        //$role3->givePermissionTo('publish articles');
        //$role3->givePermissionTo('unpublish articles');

        $role4 = Role::create(['name' => 'user']);
        //$role4->givePermissionTo('publish articles');
        //$role4->givePermissionTo('unpublish articles');

        // Relacionar roles con los usuarios
        $user = $this->repository->find(1);
        $user->assignRole($role1);

        $user = $this->repository->find(2);
        $user->assignRole($role1);

        $user = $this->repository->find(3);
        $user->assignRole($role2);

        $user = $this->repository->find(4);
        $user->assignRole($role3);

        $user = $this->repository->find(5);
        $user->assignRole($role4);

        $user = $this->repository->find(6);
        $user->assignRole($role4);
    }
}
