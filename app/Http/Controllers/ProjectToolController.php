<?php

namespace App\Http\Controllers;

use App\ProjectTool;
use App\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectToolController extends Controller
{
    public function store(Request $request)
    {
        $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(),[
                'tool_id' => 'required|exists:tools,id,deleted_at,NULL,active,1,status_tools_id,2',
                'project_id' => 'required|exists:projects,id,deleted_at,NULL,status,1',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $project= new ProjectTool();
            $project->user_id=$Auth->id;
            $project->tool_id=$request->tool_id;
            $tool=Tool::find($project->tool_id);
            $tool->status_tools_id=3;
            $project->project_id=$request->project_id;
            $project->save();
            $tool->save();
            Log::info("User with email { $Auth->email} created project tool number");
            return response()->json($project->load(['user','tool','project']), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on project tool( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    //public function update(Request $request,  $id)
    //{
    //    $Auth=Auth::user();
//
     //   try {
     //      $projectTool= projectTool::find($id);
    //      if (!$projectTool) {
    //          throw new \Exception("Project tool with id: {$id} dont exist", 500);
    //      }
    //      $validator = \Validator::make($request->all(),[
    //           'tool_id' => 'required|exists:tools,id',
    //            'project_id' => 'required|exists:projects,id',
    //           'active' =>'required'
    //      ]);
    //      if ($validator->fails()) {
    //          throw new \Exception($validator->errors()->first(), 500);
    //       }
//
    //      $projectTool->update($request->all());
    //      Log::info("User with email {$Auth->email} updated project tool number {$id} successfully");
    //      return response()->json($projectTool->load(['user','tool','project']), 200);
    //   } catch (\Exception $exception) {
    //       Log::error("User with email {$Auth->email} try access update on  project tool but is not possible!Message error({$exception->getMessage()}");
    //       return response()->json(['error' => $exception->getMessage()], $exception->getCode());
    //    }
    //  }
    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $projectTool= projectTool::find($id);
            if (!$projectTool) {
                throw new \Exception("Project with id: {$id} dont exist", 500);
            }
            $projectTool->tool->status_tools_id=2;
            $projectTool->tool->save();
            $projectTool->delete();
            Log::info("User with email {$Auth->email} deleted project tool number {$id}");
            return response()->json(['message' => 'Deleted'], 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access destroy  on project tool but  is not possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()->errors()->first()], $exception->getCode());
        }
    }
}
