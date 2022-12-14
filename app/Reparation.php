<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Inspection;
use App\Tool;
class Reparation extends Model
{
    protected $fillable = ['reason','solution','additionalDescription'];
    use SoftDeletes;
    public  function  inspection(){
        return $this->belongsTo('App\Inspection');
    }
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
