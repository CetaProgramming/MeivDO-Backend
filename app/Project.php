<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function projectTools(){
        return $this->hasMany('App\ProjectTool');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
}
