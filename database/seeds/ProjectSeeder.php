<?php

use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayName= array(
            'Barcelos','Matosinhos','Leça','Paris'
        );

        foreach ($arrayName as $array){
            \DB::table('projects')->insert([
                'name'=> 'Obra '.$array,
                'address'=>$array,
                'status'=>1,
                'startDate'=>date('2003/09/02'),
                'endDate'=>date('2003/09/02'),
                'user_id'=>2,
                'created_at'=> now(),
                'updated_at' =>now(),

            ]);
        }
    }
}
