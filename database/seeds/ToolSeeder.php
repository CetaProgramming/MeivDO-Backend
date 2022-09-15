<?php

use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayName= array(
            'MarteloPregaPregos','TesourasCortaCoisas','SerrotesSerraTudo','ChavesAbrePortas'
        );

        foreach ($arrayName as $array){
            \DB::table('tools')->insert([
                'code'      => $array ,
                'group_tools_id'=>rand(1,3),
                'status_tools_id'=>rand(1,3),
                'user_id'=>rand(1,3),
                'active'=>1,
                'created_at'=> now(),
                'updated_at' =>now(),
            ]);
        }
    }
}
