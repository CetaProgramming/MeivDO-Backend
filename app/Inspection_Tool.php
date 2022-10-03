<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection_Tool extends Model
{
    protected $fillable = ['inspection_id','tool_id'];
    use SoftDeletes;

    public function tool(){
        return $this->belongsTo('App\Tool');
    }
    public function inspection(){
        return $this->hasOne('App\Inspection');
    }
}
