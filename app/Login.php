<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Login extends User
{

    protected $table = 'users';
    protected $hidden = ['role_id', 'password', 'active', 'user_id', 'created_at', 'updated_at', 'deleted_at'];

    public function data(){

        $roleData = Role::find($this->role_id);

        $this->role = [
            "id" => $roleData->id,
            "name" => $roleData->name,
            "permissions" => $roleData->permissions($this->role_id),
        ];
        
        return $this;
    }

}
