<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Inspection extends Model
{
    protected $fillable = ['additionalDescription','status','user_id'];
    use SoftDeletes;

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function inspectionTool(){
        return DB::table('inspection_tool')
                ->where('inspection_id', '=', $this->id)->get();
    }

    public function getRelationShip(){
        return [
            "inspectionTool"=>$this->inspectionTool(),
            "inspectionProjectTool" => $this->inspectionProjectTool($this->id, 'inspection_id')
        ];
    }
}
