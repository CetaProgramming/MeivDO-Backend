<?php

use Illuminate\Database\Seeder;
use App\Component;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $components = [
            [
                'name' => 'ToolComponent',
                'feature_id' => 1
            ],
            [
                'name' => 'ProjectComponent',
                'feature_id' => 2
            ],
            [
                'name' => 'InspectionComponent',
                'feature_id' => 3
            ],
            [
                'name' => 'RepairComponent',
                'feature_id' => 4
            ],
            [
                'name' => 'UserComponent',
                'feature_id' => 5
            ]
        ];
        $componentsCount = count($components);

        for ($i = 0; $i < $componentsCount; $i++) {
            Component::insert([
                'name' => $components[$i]['name'],
                'feature_id' => $components[$i]['feature_id'],
                'created_at'=> now(),
                'updated_at' =>now(),
            ]);
        }
    }
}
