<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;

class Login extends User
{
    use HasApiTokens;

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

    public function deleteToken(Request $request){
        try {
            if($this->tokens()->count())
                $request->user()->currentAccessToken()->delete();
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        } 
    }

}
