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
        $arrayName1= array(
            'TESJAR12344','TESJAR12345','TESJAR12346'
        );
        $arrayName2= array(
            'TESALU345564','TESALU345565'
        );
        $arrayName3= array(
            'TESACO09845','TESACO09846'
        );
        $arrayName4= array(
            'MARBOR90456','MARBOR90457','MARBOR90458'
        );
        $arrayName5= array(
            'SERMAD10244','SERMAD10245'
        );
        $arrayName6= array(
            'SERMESA12220244','SERMESA12220245','SERMESA12220246','SERMESA12220247','SERMESA12220248'
        );
        $arrayName7= array(
            'SERSABRE456','SERSABRE457'
        );
            foreach ($arrayName1 as $array){
                \DB::table('tools')->insert([
                    'code'      => $array ,
                    'group_tools_id'=>1,
                    'status_tools_id'=>2,
                    'user_id'=>1,
                    'active'=>1,
                    'created_at'=> now(),
                    'updated_at' =>now(),
                ]);
            }
            foreach ($arrayName2 as $array){
                \DB::table('tools')->insert([
                    'code'      => $array ,
                    'group_tools_id'=>2,
                    'status_tools_id'=>2,
                    'user_id'=>1,
                    'active'=>1,
                    'created_at'=> now(),
                    'updated_at' =>now(),
                ]);
            }
            foreach ($arrayName3 as $array){
                \DB::table('tools')->insert([
                    'code'      => $array ,
                    'group_tools_id'=>3,
                    'status_tools_id'=>2,
                    'user_id'=>1,
                    'active'=>1,
                    'created_at'=> now(),
                    'updated_at' =>now(),
                ]);
            }
            foreach ($arrayName4 as $array){
                \DB::table('tools')->insert([
                    'code'      => $array ,
                    'group_tools_id'=>4,
                    'status_tools_id'=>2,
                    'user_id'=>1,
                    'active'=>1,
                    'created_at'=> now(),
                    'updated_at' =>now(),
                ]);
            }
            foreach ($arrayName5 as $array){
                \DB::table('tools')->insert([
                    'code'      => $array ,
                    'group_tools_id'=>7,
                    'status_tools_id'=>2,
                    'user_id'=>1,
                    'active'=>1,
                    'created_at'=> now(),
                    'updated_at' =>now(),
                ]);
            }
            foreach ($arrayName6 as $array){
                \DB::table('tools')->insert([
                    'code'      => $array ,
                    'group_tools_id'=>8,
                    'status_tools_id'=>2,
                    'user_id'=>1,
                    'active'=>1,
                    'created_at'=> now(),
                    'updated_at' =>now(),
                ]);
            }
            foreach ($arrayName7 as $array){
                \DB::table('tools')->insert([
                    'code'      => $array ,
                    'group_tools_id'=>9,
                    'status_tools_id'=>2,
                    'user_id'=>1,
                    'active'=>1,
                    'created_at'=> now(),
                    'updated_at' =>now(),
                ]);
            }
    }
}
