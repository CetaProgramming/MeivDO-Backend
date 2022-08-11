<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Component;

class Role extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function rolesRoute($id_role){
        return DB::table('component_role_route')
                  ->where('role_id', '=', $id_role)
                  ->orderBy('component_id');
    }

    public function permissions($id_role){

        $queryComponentId = $this->rolesRoute($id_role);
        $componentId = $queryComponentId->distinct()->pluck('component_id');
        $arrayPermissions = [];
        foreach ($componentId as $component){
            $queryComponentDetail = DB::table('components')->where('id', '=', $component);
            $arrayPermissions[count($arrayPermissions)] = [
                'id' => $queryComponentDetail->pluck('id')->first(),
                'component' => $queryComponentDetail->pluck('name')->first(),
                'feature' => DB::table('features')->where('id', '=', $component)->pluck('name')->first(),
                'routes' => $this->rolesRoute($id_role)
                                ->where('component_id', '=', $component)
                                ->rightJoin('routes', 'routes.id', '=', 'component_role_route.route_id')
                                ->pluck('name')
            ];
        }
        return $arrayPermissions;
    }
}
