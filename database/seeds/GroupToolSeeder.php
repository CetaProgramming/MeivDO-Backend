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

        $arrayName1= array(
            'Tesoura Jardinagem','Tesoura corta alumínio','Tesoura de Aço'
        );
        $arrayName2= array(
            'Martelo de Borracha','Martelo Marreta','Martelo madeira'
        );
        $arrayName4= array(
            'Serra Madeira','Serra Mesa','Serra sabre'
        );
        foreach ($arrayName1 as $array){
            \DB::table('group_tools')->insert([
                'code'=> $array,
                'category_tools_id'=>1,
                'description'=>'Tesoura',
                'active'=>1,
                'user_id'=>2,
                'created_at'=> now(),
                'updated_at' =>now(),

            ]);
        }
        foreach ($arrayName2 as $array){
            \DB::table('group_tools')->insert([
                'code'=> $array,
                'category_tools_id'=>2,
                'description'=>'Martelo',
                'active'=>1,
                'user_id'=>2,
                'created_at'=> now(),
                'updated_at' =>now(),

            ]);
        }
        foreach ($arrayName4 as $array){
            \DB::table('group_tools')->insert([
                'code'=> $array,
                'category_tools_id'=>4,
                'description'=>'Serra',
                'active'=>1,
                'user_id'=>2,
                'created_at'=> now(),
                'updated_at' =>now(),

            ]);
        }
    }
}
