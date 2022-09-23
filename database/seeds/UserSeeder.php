<?php

use Illuminate\Database\Seeder;
use App\User;

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
                'name' => 'Administrator',
                'email' => 'admin@site.com',
                'password' => bcrypt('admin'),
                'role_id' => 1
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@site.com',
                'password' => bcrypt('manager'),
                'role_id' => 2
            ],
            [
                'name' => 'Operator',
                'email' => 'operator@site.com',
                'password' => bcrypt('operator'),
                'role_id' => 3
            ],
        ];

        $usersCount = count($users);

        for ($i = 0; $i < $usersCount; $i++) {
            User::insert([
                'name' => $users[$i]['name'],
                'email' => $users[$i]['email'],
                'password' => $users[$i]['password'],
                'active' => 1,
                'role_id' => $users[$i]['role_id'],
                'created_at' => now(),
                'updated_at' =>now(),
            ]);
        }

    }
}
