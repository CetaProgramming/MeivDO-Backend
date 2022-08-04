<?php

use Illuminate\Database\Seeder;
use App\Route;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = ['get', 'post', 'put', 'delete'];
        $routeCount = count($routes);

        for($i = 0; $i < $routeCount; $i++){
            Route::insert([
               'name' => $routes[$i],
               'created_at' => now()
            ]);
        }
    }
}
