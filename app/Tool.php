<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tool extends Model
{
    use SoftDeletes;
    protected $fillable = ['code','active','group_tools_id','status_tools_id'];
    public function statusTools(){
        return $this->belongsTo('App\statusTool');
    }
    public function groupTools(){
        return  $this->belongsTo('App\groupTool');
    }
    public function  user(){
        return $this->belongsTo('App\user');
    }
}

