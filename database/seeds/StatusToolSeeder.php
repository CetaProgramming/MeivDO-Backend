<?php

use Illuminate\Database\Seeder;

class StatusToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayName= array(
            'PorArranjar','Disponível','Estragado'
        );

        foreach ($arrayName as $array){
            \DB::table('status_tools')->insert([
                'name'      => $array ,
                'created_at'=> now(),
                'updated_at' =>now(),
            ]);
        }
    }
}
