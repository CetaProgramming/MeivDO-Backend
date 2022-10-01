<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectTool;
use App\Tool;
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
                'address' => 'required',
                'status' => 'required|boolean',
                'startDate' => 'required|date_format:Y/m/d|after_or_equal:today',
                'endDate' => 'required|date_format:Y/m/d|after_or_equal:startDate',

            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $project= new project();
            $project->name=$request->name;
            $project->address=$request->address;
            $project->status=$request->status;
            $project->startDate=$request->startDate;
            $project->endDate=$request->endDate;
            $project->user_id=$Auth->id;
            $project->save();
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
              'address' => 'required',
              'status' => 'required|boolean',
              'startDate' => 'required|date_format:Y/m/d|after_or_equal:today',
              'endDate' => 'required|date_format:Y/m/d|after_or_equal:startDate',
         ]);
         if ($validator->fails()) {
              throw new \Exception($validator->errors()->first(), 500);
          }
          if($project->status==0){
              foreach ($project->projectTools()->get() as $projectTool){
                  $projectTool->tool->status_tools_id=4;
                  $projectTool->tool->save();
              }
          }
         $project->update($request->all());

         Log::info("User with email {$Auth->email} updated project  number {$id} successfully");
          return response()->json($project->load(['projectTools','user']), 200);
      } catch (\Exception $exception) {
        Log::error("User with email {$Auth->email} try access update on  project but is not possible!Message error({$exception->getMessage()}");
          return response()->json(['error' => $exception->getMessage()], $exception->getCode());
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

