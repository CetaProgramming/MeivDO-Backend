<?php

namespace App\Http\Controllers;
use App\groupTool;
use App\Tool;
use App\StatusTool;
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
                'groupTools' => 'nullable|exists:group_tools,id,deleted_at,NULL',
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
                !$request->groupTools ? ["group_tools_id","LIKE", $request->groupTools] : ["group_tools_id","=", (int) $request->groupTools],
                ["active", "LIKE","%{$request->active}%"],
                !$request->statusTools ? ["status_tools_id","LIKE", $request->statusTools] : ["status_tools_id","=", (int) $request->statusTools],
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
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $tool->code=$request->code;
            $tool->group_tools_id=$request->group_tools_id;
            $tool->status_tools_id=2;
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

            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            if($tool->group_tools_id != $request->group_tools_id){
                $groupTool = groupTool::find($request->group_tools_id);
                if(!$groupTool || !$groupTool->active)
                    throw new \Exception("The GroupTools is invalid!", 500);
            }

            $statusTool = StatusTool::find(2);
            if($request->active==0 && $tool->status_tools_id != 2 ){
                throw new \Exception("The tool must have the status in {$statusTool->name}", 500);
            }
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
