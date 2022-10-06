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

    static public function inspectionProjectTool($projectToolId){
        return DB::table('inspection_projecttool')
        ->where('project_tools_id', '=', $projectToolId)->get();
    }

    static public function addInspectionProjectTool($projectToolId){
        DB::table('inspection_projecttool')->insert([
            'inspection_id' => NULL,
            'project_tools_id' => $projectToolId
        ]);
    }

    static public function remInspectionProjectTool($projectToolId){
        DB::table('inspection_projecttool')->where('project_tools_id', '=', $projectToolId)->delete();
    }
}
