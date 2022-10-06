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

    static public function inspectionProjectTool($projectToolId, $data = 'project_tools_id'){
        return DB::table('inspection_projecttool')
        ->where($data, '=', $projectToolId)->get();
    }

    static public function updInspectionId($projectToolId, $inspectionId){
        DB::table('inspection_projecttool')
        ->where('id', '=', $projectToolId)
        ->update(['inspection_id' => $inspectionId]);
    }

    static public function updStatusTool($projectToolId, $status){
        $data = DB::table('inspection_projecttool')
                    ->Rightjoin('project_tools', 'project_tools.id', '=', 'inspection_projecttool.project_tools_id')
                    ->select('project_tools.tool_id')
                    ->where('inspection_projecttool.id', '=', $projectToolId)
                    ->get();

        $tool = Tool::find($data[0]->tool_id);
        $tool->status_tools_id = $status;
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
