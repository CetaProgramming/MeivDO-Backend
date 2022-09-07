<?php

use Illuminate\Database\Seeder;

class GroupToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayName= array(
            'Portugal','Espanha','França','Polónia'
        );

        foreach ($arrayName as $array){
            \DB::table('group_tools')->insert([
                'code'=> $array,
                'image'=>null,
                'category_tools_id'=>rand(1,3),
                'description'=>'dd',
                'active'=>1,
                'user_id'=>2,
                'created_at'=> now(),
                'updated_at' =>now(),

            ]);
        }
    }
}
