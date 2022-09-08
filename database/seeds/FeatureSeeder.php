<?php

use Illuminate\Database\Seeder;
use App\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = ['tools', 'projects', 'inspections', 'repairs', 'users'];
        $featuresCount = count($features);

        for ($i = 0; $i < $featuresCount; $i++) {
            Feature::insert([
                'name' => $features[$i],
                'created_at' => now()
            ]);
        }

    }
}
