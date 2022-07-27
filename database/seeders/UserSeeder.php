<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;

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
                'surname' => 'Chirinos',
                'email' => 'lchirinos@acercapartners.com',
                'username' => 'lchirinos',
                'password' => bcrypt('test'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Manuel',
                'surname' => 'Agapito',
                'email' => 'manu.agapito@wpark.com',
                'username' => 'maag',
                'password' => bcrypt('test'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Vicente',
                'surname' => 'Catalá',
                'email' => 'vicente.catala@wpark.com',
                'username' => 'vica',
                'password' => bcrypt('test'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        User::insert($users);

        // Relaciones con los usuarios
        $user1 = User::find(1);
        $user1->compounds()->sync([1,2]);
        // $user1->devices()->sync([1]);

        $user2 = User::find(2);
        $user2->compounds()->sync([1]);
        // $user2->devices()->sync([2]);

        $user3 = User::find(3);
        $user3->compounds()->sync([1,2]);
        // $user3->devices()->sync([3]);
    }
}
