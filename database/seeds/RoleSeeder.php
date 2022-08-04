<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $roles = ['administrator', 'manager', 'operator'];
        $roleCount = count($roles);

        for ($i = 0; $i < $roleCount; $i++) {
            Role::insert([
                'name' => $roles[$i],
                'created_at' => now()
            ]);
        }
    }
}
