<?php

namespace App;

use Faker\Core\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Collection;

class Inspection extends Model
{
    protected $fillable = ['additionalDescription','status','user_id'];
    use SoftDeletes;

    public function user(){
        return $this->belongsTo('App\User');
    }
    public  function  reparation(){
        return $this->hasOne('App\Reparation');
    }

    /**
     * Get the inspectionTool of inspection
     * @param string $column
     * @param  integer $value
     * @return \Illuminate\Support\Collection
     */
    public function inspectionTool($column="inspection_id",$value=null){

        return DB::table('inspection_tool')
                ->where($column, '=',$value ?? $this->id)->get();
    }
    /**
     * Get details inspection (project and tool)
     * @return void
     */
    public function getRelationToolOrProjectTool(){

        $this->inspectionDetails = $this->getRelationShipTable();

    }
    /**
     * Get the table of inspectionTool or inspectionProject of inspection
     * @return array
     */
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
    /**
     * Get the relationShip of inspectionTool or inspectionProject of inspection
     * @return array
     */
    public function getRelationShip(){
        if($this->inspectionTool()->count()){
            return ["inspectionTool",$this->inspectionTool()];
        }
        return ["inspectionProjectTool", $this->inspectionProjectTool($this->id, 'inspection_id')];
    }
    /**
     * Update status tool of inspection
     * @param  array $relationShip
     * @param  boolean $status
     * @return void
     */
    public function  updStatusTool($relationShip,$status){
        if($relationShip[0]=="inspectionTool"){
            $this->updToolStatusTool($relationShip[1][0]->tool_id,$status);
        }elseif($relationShip[0]=="inspectionProjectTool"){
            $this->updProjectStatusTool($relationShip[1][0]->id,$status);
        }
    }
    /**
     * Update status tool
     * @param  integer $tool_id
     * @param  boolean $status
     * @return void
     */
    public function updToolStatusTool($tool_id,$status){
        $tool = Tool::find($tool_id);
        $tool->status_tools_id=$status;
        $tool->save();
    }
    /**
     * InspectionProjectTool of inspection
     * @param  integer $projectToolId
     * @param  string $data
     * @return \Illuminate\Support\Collection
     */
    static public function inspectionProjectTool($projectToolId, $data = 'project_tools_id'){
        return DB::table('inspection_projecttool')
        ->where($data, '=', $projectToolId)->get();
    }
    /**
     * Missing Inspections
     * @param  boolean $withGet
     * @return  \Illuminate\Support\Collection | \Illuminate\Database\Query\Builder
     */
    static  public  function  missingInspections($withGet = true){
        $data = DB::table('inspection_projecttool')
        ->where('inspection_id', '=', null);
        return $withGet ? $data->get() : $data;
    }
    /**
     * Update Inspection id
     * @param  integer $projectToolId
     * @param  integer $inspectionId
     * @return void
     */
    static public function updInspectionId($projectToolId, $inspectionId){
        DB::table('inspection_projecttool')
        ->where('id', '=', $projectToolId)
        ->update(['inspection_id' => $inspectionId]);
    }
    /**
     * Update project Status Tool
     * @param  integer $projectToolId
     * @param  boolean $status
     * @return void
     */
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
    /**
     * Add an Inspection Project Tool
     * @param  integer $projectToolId
     * @return void
     */
    static public function addInspectionProjectTool($projectToolId){
        DB::table('inspection_projecttool')->insert([
            'inspection_id' => NULL,
            'created_at'=> now(),
            'updated_at' => now(),
            'project_tools_id' => $projectToolId
        ]);
    }
    /**
     * Add an Inspection Project Tool
     * @param  integer $projectToolId
     * @return void
     */
    static public function updInspectionProjectTool($projectToolId){
        DB::table('inspection_projecttool')->where('id', $projectToolId)
        ->update([
            'inspection_id' => NULL,
            'updated_at' => now()
        ]);
    }

    /**
     * Inspection Project Tool
     * @param  integer $projectToolId
     * @return void
     */
    static public function remInspectionProjectTool($projectToolId){
        DB::table('inspection_projecttool')->where('project_tools_id', '=', $projectToolId)->delete();
    }
    /**
     * Show the LastRow
     * @param  \Illuminate\Database\Query\Builder $table
     * @param integer $tool_id
     * @param  DateTime $date
     * @return string
     */
    public  function  LastRow($table,$tool_id,$date){

        $filterTable= $table->where("tool_id", '=',$tool_id);
        if($filterTable->get()->isEmpty()){
            $inspectionDateTime= explode(' ', $this->created_at->ToDateTimeString());
            $inspectionDateTime[0] =  Carbon::parse(  $inspectionDateTime[0].' -30 days')->ToDateString();
            return $inspectionDateTime[0].' '.$inspectionDateTime[1];
        }
        $filterTableNew = $filterTable->orderBy($date, 'desc')->first();
        $inspectionDate = Inspection::find($filterTableNew->inspection_id);
        return  $inspectionDate->created_at;
    }
    /**
     * Project Tools with join
     * @return \Illuminate\Database\Query\Builder
     */
    public  function  GetProjectToolsWithJoin(){
        return DB::table('inspection_projecttool')
            ->Rightjoin('project_tools', 'project_tools.id', '=', 'inspection_projecttool.project_tools_id');
    }
    /**
     * Inspection Tool id
     * @return integer
     */
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
    /**
     * validate if the inspection is the last inspection
     * @param integer $tool_id
     * @return boolean
     */
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
     * validate if the  inspection can be deleted and delete the relation of the inspection
     * @return boolean
     */
    public  function  validateDelete(){

        $tool_id =$this->GetInspectionToolId();

        $tool = Tool::find($tool_id);
        if(($tool->status_tools_id == 1 ||$tool->status_tools_id == 2) && $this->isLastInspection($tool_id)==true){
            $reparationGet = Reparation::where('inspection_id',$this->id)->get();
            if($reparationGet->count()) {
                $reparation= $reparationGet[0];
                if($reparation->status ==1){
                    throw new \Exception("Inspection with id: {$this->id} cannot be deleted because reparation is close", 500);
                }
                $reparation->delete();
            }
                if($this->getRelationShip()[0]=="inspectionTool"){
                    $this->updToolStatusTool($tool_id,2);
                    Inspection_Tool::where('inspection_id','=',$this->id)->delete();
                }elseif($this-> getRelationShip()[0]=="inspectionProjectTool"){
                    $inspectionProjectTool= $this->inspectionProjectTool($this->id, $data = 'inspection_id');
                    self::updInspectionProjectTool($inspectionProjectTool[0]->id);
                    $this->updToolStatusTool($tool_id,4);
                }

            return true;
        }else{
            return false;
        }
    }
}
