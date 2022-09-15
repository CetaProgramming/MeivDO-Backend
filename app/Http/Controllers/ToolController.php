<?php

namespace App\Http\Controllers;

use App\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ToolController extends Controller
{

    public function index()
    {
        $Auth=Auth::user();

        try {
            Log::info("User with email {$Auth->email} get groupTools successfully");
            return response()->json(Tool::with(['statusTools','groupTools','user'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get groupTools but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
    public function store(Request $request)
    {
        $Auth=Auth::user();
        $Tool= new Tool();
        try {
            $validator = \Validator::make($request->all(),[
                'code'     => 'required|unique:tools',
                'group_tools_id'       => 'required|exists:group_tools,id',
                'status_tools_id' =>'required|exists:status_tools,id',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $Tool->code=$request->code;
            $Tool->group_tools_id=$request->group_tools_id;
            $Tool->status_tools_id=$request->status_tools_id;
            $Tool->active=1;
            $Tool->user_id=$Auth->id;
            $Tool->save();
            Log::info("User with email { $Auth->email} created groupTool number { $Tool->id}");
            return response()->json($Tool->load(['statusTools','groupTools','user']), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on groupTool( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
