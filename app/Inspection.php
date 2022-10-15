<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Inspection extends Model
{
    protected $fillable = ['additionalDescription','status','user_id'];
    use SoftDeletes;

    public function user(){
        return $this->belongsTo('App\User');
    }
    public function inspectionTool($column="inspection_id",$value=null){

        return DB::table('inspection_tool')
                ->where($column, '=',$value ?? $this->id)->get();
    }
    public function getRelationToolOrProjectTool(){

        $this->inspectionDetails = $this->getRelationShipTable();

    }
    function getRelationShipTable(){
        if($this->inspectionTool()->count()>0){
            $tool = Tool::find($this->inspectionTool()[0]->tool_id)->load(['statusTools', 'groupTools', 'user']);
            return ["tool"=>$tool];
        }

            $inspectionProject= $this->inspectionProjectTool($this->id, 'inspection_id')[0];
            $project_tool = ProjectTool::find($inspectionProject->project_tools_id);
            $tool = Tool::find($project_tool->tool_id)->load(['statusTools', 'groupTools', 'user']);
            $project = Project::find($project_tool->project_id);


       return  ["tool"=>$tool,"project"=>$project];


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
    static  public  function  missingInspections($withGet = true){
        $data = DB::table('inspection_projecttool')
        ->where('inspection_id', '=', null);
        return $withGet ? $data->get() : $data;
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
            'created_at'=> now(),
            'updated_at' => now(),
            'project_tools_id' => $projectToolId
        ]);
    }
    static public function remInspectionProjectTool($projectToolId){
        DB::table('inspection_projecttool')->where('project_tools_id', '=', $projectToolId)->delete();
    }
    public  function  LastRow($table,$tool_id,$date){


        $filterTable=  $table->where("tool_id", '=',$tool_id);
       if($filterTable->get()->isEmpty()){
           $inspectionDateTime= explode(' ', $this->created_at->ToDateTimeString());
           $inspectionDateTime[0] =  Carbon::parse(  $inspectionDateTime[0].' -30 days')->ToDateString();
           return $inspectionDateTime[0].' '.$inspectionDateTime[1];
       }
        return $filterTable
           ->orderBy($date, 'desc')->first()->created_at;
    }
    public  function  GetProjectToolsWithJoin(){
        return DB::table('inspection_projecttool')
            ->Rightjoin('project_tools', 'project_tools.id', '=', 'inspection_projecttool.project_tools_id');
    }
    public function  GetInspectionToolId(){
        $tool_id=0;
        if($this->getRelationShip()[0]=="inspectionTool"){
            $tool_id = $this->getRelationShip()[1][0]->tool_id;

        }elseif($this->getRelationShip()[0]=="inspectionProjectTool"){
            $dataWithRelation = $this->GetProjectToolsWithJoin();
            $dataWithRelation->select('project_tools.tool_id')
                ->where('inspection_projecttool.id', '=', $this->getRelationShip()[1][0]->id)
                ->get();
            $tool_id = Tool::find($dataWithRelation->get()[0]->tool_id)->id;
        }
        return $tool_id;
    }
    public function isLastInspection($tool_id){

        $data = $this->GetProjectToolsWithJoin();
        $inspectionDateTime= explode(' ', $this->created_at->ToDateTimeString());
        $lastInspectionToolDateTime= explode(' ', $this->LastRow(DB::table('inspection_tool'),$tool_id,'created_at'));
        $projectToolTable =$data->where('inspection_id','!=',null);
        $lastProjectToolTableDateTime = explode(' ',$this->LastRow($projectToolTable,$tool_id,'inspection_projecttool.created_at'));
        $lastDateInspections=0;
       if($lastInspectionToolDateTime[0] > $lastProjectToolTableDateTime[0] ||$lastInspectionToolDateTime[0] == $lastProjectToolTableDateTime[0] &&   $lastInspectionToolDateTime[1] > $lastProjectToolTableDateTime[1]){
            $lastDateInspections = $lastInspectionToolDateTime ;
        }else{
            $lastDateInspections = $lastProjectToolTableDateTime;
        }
        if ($inspectionDateTime[0] ==$lastDateInspections[0] && $inspectionDateTime[1] ==$lastDateInspections[1]){
            return true;
        }
           return  false;
    }

    /**
     * comentário
     * @param
     * @return boolean
     */
    public  function  validateDelete(){

        $tool_id =$this->GetInspectionToolId();


        // Only delete inspection when not exist a reparation associate;
        $tool = Tool::find($tool_id);

        if(($tool->status_tools_id == 1 ||$tool->status_tools_id == 2) && $this->isLastInspection($tool_id)==true){
            if($this-> getRelationShip()[0]=="inspectionTool"){
                $this->updToolStatusTool($tool_id,2);
                $inspectionTool= Inspection_Tool::where('inspection_id','=',$this->id)->delete();
                //Delete Inspection line
            }elseif($this-> getRelationShip()[0]=="inspectionProjectTool"){
               $inspectionProjectTool= $this->inspectionProjectTool($this->id, $data = 'inspection_id');
               $inspectionProjectTool->inspection_id = null;
               $inspectionProjectTool->save();
               $tool->status_tools_id = 4;
               $tool->save();
            }
            return true;
        }else{
            return false;
        }



    }
}
