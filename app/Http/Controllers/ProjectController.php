<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectTool;
use App\Tool;
use App\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        $Auth=Auth::user();

        try {
            Log::info("User with email {$Auth->email} get projects successfully");
            return response()->json(Project::with(['projectTools','user'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get projects but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
    public function searchData(Request $request){

        $Auth=Auth::user();
        try {

            $validator = \Validator::make($request->all(), [
                'status' => 'nullable|boolean',
                $request->startDate && 'startDate' => 'nullable|date_format:Y/m/d',
                $request->endDate  && 'endDate' => 'nullable|date_format:Y/m/d'
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            Log::info("User with email { $Auth->email} made a search on table projects");

            return response()->json(Project::where([
                    ["name", "LIKE", "%{$request->name}%"],
                    ["status", "LIKE", "%{$request->status}%"]
                ])->when( $request->startDate !='' ,function ($project){
                    return $project->where("startDate", ">=", request('startDate'));
                })->when( $request->endDate !='' ,function ($project){
                return $project->where("endDate", "<=", request('endDate'));
            })
                ->with(['projectTools','user'])->paginate(), 200);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on search projects( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function store(Request $request)
    {
        $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(),[
                'name' => 'required|unique:projects,name,null,id,deleted_at,NULL',
                'startDate' => 'nullable|date_format:Y/m/d|after_or_equal:today',
                'endDate' => 'nullable|date_format:Y/m/d|after_or_equal:startDate',
                'tools' => 'nullable|array'
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }

            $project= new project();
            $project->name=$request->name;
            $project->address= $request->address ?? null;
            $project->status=1;
            $project->startDate= $request->startDate ?? explode(' ', now())[0];
            $project->endDate= $request->endDate;
            $project->user_id=$Auth->id;
            $project->save();
            if($request->tools)
                foreach ($request->tools as $tool) {
                    $toolInstance=Tool::find($tool);
                    if(!$toolInstance || $toolInstance->status_tools_id != 2 || !$toolInstance->active)
                        continue;
                    $projectTool = new ProjectTool();
                    $projectTool->tool_id = $tool;
                    $projectTool->project_id = $project->id;
                    $projectTool->user_id = $Auth->id;
                    $projectTool->save();
                    $toolInstance->status_tools_id=3;
                    $toolInstance->save();
                }

            Log::info("User with email { $Auth->email} created project number { $project->id}");
            return response()->json($project->load(['projectTools','user']), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on project ( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function update(Request $request,  $id)
    {
        $Auth=Auth::user();

      try {
        $project= project::find($id);
        if (!$project) {
            throw new \Exception("Project  with id: {$id} dont exist", 500);
        }
        $validator = \Validator::make($request->all(),[
            'name' => 'required|unique:projects,name,'.$project->id.',id,deleted_at,NULL',
            'startDate' => 'date_format:Y/m/d|after_or_equal:today',
            'endDate' => 'nullable|date_format:Y/m/d|after_or_equal:startDate',
            'tools' => 'nullable|array'
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first(), 500);
        }
        $project->name=$request->name;
        $request->address && $project->address= $request->address;
        $request->startDate && $project->startDate = $request->startDate;
        $request->endDate && $project->endDate=$request->endDate;
        $project->user_id=$Auth->id;

        $request->tools ?? $request->tools=[];

            foreach ($project->projectTools as $projectTool) {
                if(in_array($projectTool->tool_id, $request->tools))
                    continue;
                $projectTool = ProjectTool::find($projectTool->id);
                $projectTool->delete();
                $toolInstance= Tool::find($projectTool->tool_id);
                $toolInstance->status_tools_id=2;
                $toolInstance->save();
            }
            foreach ($request->tools as $tool) {
                $toolInstance=Tool::find($tool);
                if(!$toolInstance || $toolInstance->status_tools_id != 2 || !$toolInstance->active)
                    continue;
                    $projectTool = new ProjectTool();
                    $projectTool->tool_id = $tool;
                    $projectTool->project_id = $project->id;
                    $projectTool->user_id = $Auth->id;
                    $projectTool->save();
                    $toolInstance= Tool::find($tool);
                    $toolInstance->status_tools_id = 3;
                    $toolInstance->save();
        }

        $project->save();
        Log::info("User with email {$Auth->email} updated project  number {$id} successfully");
        return response()->json($project->load(['projectTools','user']), 200);
      } catch (\Exception $exception) {
        Log::error("User with email {$Auth->email} try access update on  project but is not possible!Message error({$exception->getMessage()}");
        return response()->json(['error' => $exception->getMessage()], $exception->getCode());
       }
    }

    /**
     * Change Status The project is close or open
     *
     * @param int $projectId
     * @return Response/Json
     *  */

    public function changeStatusProject(int $projectId){
        try {
            $project = Project::find($projectId);
            if (!$project) {
                throw new \Exception("Project  with id: {$projectId} dont exist", 500);
            }
            if($project->status){
                $this->addProjectToolsOnInspection($project);
            }
            if(!$project->status){
                $this->verifyAnyInpectionsIsDid($project);
                $this->removeProjectToolsInInspections($project);
            }
            $project->status = $project->status ? 0 : 1;
            $project->save();

            return response()->json($project->load(['projectTools','user']), 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Verify if project has inspections finished
     *
     * @param int $projectId
     * @return Response/Json
     *  */

    private function verifyAnyInpectionsIsDid(Project $project){
        $dataProjectToolsRelationShip = $project->projectTools;
        if(!$dataProjectToolsRelationShip->count()){
            return;
        }
        for($i=0; $i < $dataProjectToolsRelationShip->count(); $i++){
            $dataInspection = Inspection::inspectionProjectTool($dataProjectToolsRelationShip[$i]->id);
            if(is_null($dataInspection))
            return;
            if(!is_null($dataInspection[0]->inspection_id))
                throw new \Exception("The ProjectTool with id {$dataProjectToolsRelationShip[$i]->id} has a inspection finished, this way cannot change status project!", 500);
        }
    }

     /**
     * Remove inspection on table inspections_projecttools
     *
     * @param Project $projectId
     * @return void
     *  */

    private function removeProjectToolsInInspections(Project $project){
        $dataProjectToolsRelationShip = $project->projectTools;
        for($i=0; $i < $dataProjectToolsRelationShip->count(); $i++){
            $tool = Tool::find($dataProjectToolsRelationShip[$i]->tool_id);
            $tool->status_tools_id = 3;
            $tool->save();
            Inspection::remInspectionProjectTool($dataProjectToolsRelationShip[$i]->id);
        }
    }

     /**
     * Add inspection on table inspections_projecttools
     *
     * @param Project $projectId
     * @return void
     *  */

    private function addProjectToolsOnInspection(Project $project){
        $dataProjectToolsRelationShip = $project->projectTools;
        for($i=0; $i < $dataProjectToolsRelationShip->count(); $i++){
            $tool = Tool::find($dataProjectToolsRelationShip[$i]->tool_id);
            $tool->status_tools_id = 4;
            $tool->save();
            Inspection::addInspectionProjectTool($dataProjectToolsRelationShip[$i]->id);
        }
    }

    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $project= project::find($id);
            if (!$project) {
                throw new \Exception("Project with id: {$id} dont exist", 500);
            }
            if($project->status==1) {
                foreach ($project->projectTools()->get() as $projectTool) {
                    $projectTool->tool->status_tools_id = 2;
                    $projectTool->tool->save();
                }
            }
            $project->delete();
            Log::info("User with email {$Auth->email} deleted project number {$id}");
            return response()->json(['message' => 'Deleted'], 200);
        } catch (Exception $exception) {
            Log::error("User with email {$Auth->email} try access destroy  on project but  is not possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()->errors()->first()], $exception->getCode());
        }
    }
}

