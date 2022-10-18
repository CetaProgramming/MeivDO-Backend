<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reparation extends Model
{
    protected $fillable = ['tool_id','project_id','user_id'];

    use SoftDeletes;

    public  function  createReparation($Auth,$inspectionId){
        $this->inspection_id = $inspectionId;
        $this->reason ="";
        $this->solution= "";
        $this->additionalDescription ="";
        $this->user_id = $Auth->id;
        $this->status=0;
        $this->save();
    }
}
