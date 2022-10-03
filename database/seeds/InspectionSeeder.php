<?php

use Illuminate\Database\Seeder;

class InspectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayName= array(
            'O martelo está partido','O motor da motoserra não funciona','Está danificado','Não tem a cabeça'
        );

        foreach ($arrayName as $array){
            \DB::table('inspections')->insert([
                'additionalDescription'=> $array,
                'status'=>1,
                'user_id'=>2,
                'created_at'=> now(),
                'updated_at' =>now(),

            ]);
        }
    }
}
