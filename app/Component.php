<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $hidden = ['feature_id','created_at', 'updated_at', 'deleted_at', 'pivot'];

    public function feature(){
        return "sadsad";
    }
}
