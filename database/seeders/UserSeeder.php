<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Luiggi',
                'email' => 'lchirinos@acercapartners.com',
                'username' => 'lchirinos',
                'password' => bcrypt('test'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'Jesús',
                'email' => 'jmora@acercapartners.com',
                'username' => 'jmora',
                'password' => bcrypt('test'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]

        ];

        // Creación de 4 usuarios fake y se agregan a los 2 usuarios principales
        $fakeUsers = User::factory(4)->raw();

        foreach($fakeUsers as $fakeUser){
            array_push($users, $fakeUser);
        }

        User::insert($users);

        // Relacionar campas con los usuarios
        $user1 = User::find(1);
        $user1->compounds()->sync([1,2]);

        $user2 = User::find(2);
        $user2->compounds()->sync([1]);
    }
}
