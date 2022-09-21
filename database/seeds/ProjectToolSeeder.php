<?php

use Illuminate\Database\Seeder;

class ProjectToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayName= array(
            1,2,1,2
        );

        foreach ($arrayName as $array){
            \DB::table('project_tools')->insert([
                'tool_id'=>rand(1,3),
                'project_id'=>rand(1,3),
                'user_id'=>$array,
                'created_at'=> now(),
                'updated_at' =>now(),

            ]);
        }
    }
}

