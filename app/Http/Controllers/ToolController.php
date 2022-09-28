<?php

namespace App\Http\Controllers;
use App\groupTool;
use App\Tool;
use App\Helpers\Active;
use App\Helpers\IsDeleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{

    public function index()
    {
        $Auth=Auth::user();

        try {
            Log::info("User with email {$Auth->email} get Tools successfully");
            return response()->json(Tool::with(['statusTools','groupTools','user','projectTools'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get Tools but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
    public function searchData(Request $request){

        $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(), [
                'groupTools' => 'nullable|exists:group_tools,id,deleted_at,NULL,active,1',
                'statusTools' =>'nullable|exists:status_tools,id,deleted_at,NULL',
                'active' => 'nullable|boolean'
            ]);
            if ($validator->fails()) {
                $responseArr['message'] = $validator->errors()->first();
                return response()->json($responseArr, 500);
            }

            Log::info("User with email { $Auth->email} made a search on table tools");
            return response()->json(Tool::where([
                ["code", "LIKE", "%{$request->code}%"],
                ["group_tools_id","LIKE", "%{$request->groupTools}%"],
                ["active", "LIKE","%{$request->active}%"],
                ["status_tools_id","LIKE", "%{$request->statusTools}%"]

            ])
                ->with(['statusTools','groupTools','user','projectTools'])->paginate(), 200);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on search tools( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function store(Request $request)
    {
        $Auth=Auth::user();
        $tool= new Tool();
        try {
            $validator = \Validator::make($request->all(),[
                'code'     => 'required|unique:tools,code,null,id,deleted_at,NULL',
                'group_tools_id'       => 'required|exists:group_tools,id,deleted_at,NULL,active,1',
                'status_tools_id' =>'required|exists:status_tools,id,deleted_at,NULL',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $tool->code=$request->code;
            $tool->group_tools_id=$request->group_tools_id;
            IsDeleted::verifyDeleted(groupTool::onlyTrashed()->find($request->group_tools_id));
            Active::verifyActive(groupTool::find($request->group_tools_id));
            $tool->status_tools_id=$request->status_tools_id;
            $tool->active=1;
            $tool->user_id=$Auth->id;
            $tool->save();
            Log::info("User with email { $Auth->email} created Tool number { $tool->id}");
            return response()->json($tool->load(['statusTools','groupTools','user']), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on Tool( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function update(Request $request,  $id)
    {
        $Auth=Auth::user();

        try {
            $tool= Tool::find($id);
            if (!$tool) {
                throw new \Exception("Tool with id: {$id} dont exist", 500);
            }
            $validator = \Validator::make($request->all(),[
                'code'     => 'required|unique:tools,code,'.$tool->id.',id,deleted_at,NULL',
                'active'=>'required|boolean',
                'group_tools_id'       => 'required|exists:group_tools,id,deleted_at,NULL,active,1',
                'status_tools_id' =>'required|exists:status_tools,id,deleted_at,NULL',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            IsDeleted::verifyDeleted(groupTool::onlyTrashed()->find($request->group_tools_id));
            Active::verifyActive(groupTool::find($request->group_tools_id));
            $tool->user_id=$Auth->id;

            $tool->update($request->all());
            Log::info("User with email {$Auth->email} updated Tool number {$id} successfully");
            return response()->json($tool->load(['statusTools','groupTools','user']), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access update on Tool but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $tool= Tool::find($id);
            if (! $tool) {
                throw new \Exception("Tool with id: {$id} dont exist", 500);
            }
            $tool->delete();
            Log::info("User with email {$Auth->email} deleted Tool number {$id}");
            return response()->json(['message' => 'Deleted'], 200);
        } catch (Exception $exception) {
            Log::error("User with email {$Auth->email} try access destroy  on Tool but  is not possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
