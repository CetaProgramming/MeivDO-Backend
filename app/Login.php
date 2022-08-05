<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;
use Laravel\Sanctum\HasApiTokens;

class Login extends User
{
    use HasApiTokens;

    protected $table = 'users';
    protected $hidden = ['role_id', 'password', 'active', 'user_id', 'created_at', 'updated_at', 'deleted_at'];

    public function data(){

        $roleData = Role::find($this->role_id);

        if($this->tokens()->count())
            $this->tokens()->delete();

        $token = $this->createToken('access_aplication')->plainTextToken;

        $this->role = [
            "id" => $roleData->id,
            "name" => $roleData->name,
            "permissions" => $roleData->permissions($this->role_id),
            "token" => [
                "access_token" => $token,
                "type" => "Bearer"
            ]
        ];
        return $this;
    }

}
