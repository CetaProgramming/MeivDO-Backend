<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        Storage::deleteDirectory('public/images' );
        $this->call(RoleSeeder::class);
        $this->call(RouteSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(ComponentSeeder::class);
        $this->call(ComponentRoleRouteSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategoryToolSeeder::class);
        $this->call(GroupToolSeeder::class);
        $this->call(StatusToolSeeder::class);
        $this->call(ToolSeeder::class);
     //   $this->call(ProjectSeeder::class);
        //$this->call(ProjectToolSeeder::class);
    }
}
