<?php

use Illuminate\Database\Seeder;

class ComponentRoleRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $componentsRoleRoute = [

            // Administrator

            [
                'role_id' => 1,
                'route_id' => [1,2,3,4],
                'component_id' => 1
            ],
            [
                'role_id' => 1,
                'route_id' => [1,2,3,4],
                'component_id' => 2
            ],
            [
                'role_id' => 1,
                'route_id' => [1,2,3,4],
                'component_id' => 3
            ],
            [
                'role_id' => 1,
                'route_id' => [1,2,3,4],
                'component_id' => 4
            ],
            [
                'role_id' => 1,
                'route_id' => [1,2,3,4],
                'component_id' => 5
            ],

            // Manager

            [
                'role_id' => 2,
                'route_id' => [1,2,3,4],
                'component_id' => 1
            ],
            [
                'role_id' => 2,
                'route_id' => [1,2,3,4],
                'component_id' => 2
            ],
            [
                'role_id' => 2,
                'route_id' => [1,2,3,4],
                'component_id' => 3
            ],
            [
                'role_id' => 2,
                'route_id' => [1,2,3,4],
                'component_id' => 4
            ],

            // Operator

            [
                'role_id' => 3,
                'route_id' => [1],
                'component_id' => 1
            ],
            [
                'role_id' => 3,
                'route_id' => [1],
                'component_id' => 2
            ],
            [
                'role_id' => 3,
                'route_id' => [1,2,3,4],
                'component_id' => 3
            ],
            [
                'role_id' => 3,
                'route_id' => [1,2,3,4],
                'component_id' => 4
            ]

            
            
        ];

        $componentsRoleRouteCount = count($componentsRoleRoute);

        for ($i = 0; $i < $componentsRoleRouteCount; $i++) {
            $routesCount = count($componentsRoleRoute[$i]['route_id']);
            for($j= 0;$j < $routesCount; $j++) {
                \DB::table('component_role_route')->insert([
                    'role_id' => $componentsRoleRoute[$i]['role_id'],
                    'route_id' => $componentsRoleRoute[$i]['route_id'][$j],
                    'component_id' => $componentsRoleRoute[$i]['component_id'],
                    'created_at' => now()
                ]);
            }
        }
    }
}
