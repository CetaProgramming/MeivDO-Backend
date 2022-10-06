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
        if($this->inspectionTool()->count()){
            return ["inspectionTool",$this->inspectionTool()];
        }
        return ["inspectionProjectTool", $this->inspectionProjectTool($this->id, 'inspection_id')];
    }
    public function  updStatusTool($relationShip,$status){


        if($relationShip[0]=="inspectionTool"){

            $this->updToolStatusTool($relationShip[1][0]->tool_id,$status);
        }elseif($relationShip[0]=="inspectionProjectTool"){
            $this->updProjectStatusTool($relationShip[1][0]->id,$status);
        }
    }
    public function  updToolStatusTool($tool_id,$status){
        $tool = Tool::find($tool_id);
        if($status == 1){
            $tool->status_tools_id=2;
        }else{
            $tool->status_tools_id=1;
        }
        $tool->save();
    }
    static public function inspectionProjectTool($projectToolId, $data = 'project_tools_id'){
        return DB::table('inspection_projecttool')
        ->where($data, '=', $projectToolId)->get();
    }
    static  public  function  missingInspections(){

        return DB::table('inspection_projecttool')
            ->where('inspection_id', '=', null)->get();
    }
    static public function updInspectionId($projectToolId, $inspectionId){
        DB::table('inspection_projecttool')
        ->where('id', '=', $projectToolId)
        ->update(['inspection_id' => $inspectionId]);
    }

    static public function updProjectStatusTool($projectToolId, $status){
        $data = DB::table('inspection_projecttool')
                    ->Rightjoin('project_tools', 'project_tools.id', '=', 'inspection_projecttool.project_tools_id')
                    ->select('project_tools.tool_id')
                    ->where('inspection_projecttool.id', '=', $projectToolId)
                    ->get();


        $tool = Tool::find($data[0]->tool_id);

        $tool->status_tools_id = $status ? 2 : 1;
        $tool->save();
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
