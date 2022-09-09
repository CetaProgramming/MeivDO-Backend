<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusTool extends Model
{

    public function tools(){
       return  $this->hasMany('App\tool','status_tools_id','id');
    }

    use SoftDeletes;

}
