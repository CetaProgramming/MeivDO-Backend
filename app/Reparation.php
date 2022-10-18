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
    public  function updateReparation($inspection,$request,$Auth){
        if($this != null) {
            if ($this->status == 1) {
                throw new \Exception("Inspection with id: {$inspection->id} cannot be updated because reparation is finish", 500);
            }else{
                if($request->status == 0){
                    $reparation = new Reparation();
                    $reparation->createReparation($Auth,$inspection->id);
                }else{
                    $this->delete();
                }
            }
        }
    }
}
