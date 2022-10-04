<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    protected $fillable = ['additionalDescription','status',  'user_id'];
    use SoftDeletes;

    public function tool(){
        return $this->belongsTo('App\Tool');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function inspectionTool(){
        return $this->hasOne('App\InspectionTool');
    }
}
