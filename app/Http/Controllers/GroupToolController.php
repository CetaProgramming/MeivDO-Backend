<?php

namespace App\Http\Controllers;

use App\groupTool;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GroupToolController extends Controller
{

    public function index()
    {
        $Auth=Auth::user();

        try {

            Log::info("User with email {$Auth->email} get groupTools successfully");
            return response()->json(GroupTool::with(['categoryTools'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get groupTools but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }

    public function store(Request $request)
    {
          $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(),[
                'code'     => 'required|unique:group_tools',
                'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category_tools_id' =>'required|exists:category_tools,id',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $groupTool= new groupTool();
            $groupTool->code=$request->code;
            $groupTool->category_tools_id=$request->category_tools_id;
            $groupTool->description=$request->description;
            $groupTool->active=1;
            $groupTool->user_id=$Auth->id;
            $groupTool->save();
            if ($request->file('image')) {
                $imagePath = $request->file('image');
                $imageName =  Str::of($imagePath->getClientOriginalName())->split('/[\s.]+/');
                $path = $request->file('image')->storeAs('images/groupTool/' .  $groupTool->id, $groupTool->id."_profile.". $imageName[1], 'public');
                $groupTool->image=$path;
            }
            $groupTool->save();
            Log::info("User with email { $Auth->email} created groupTool number { $groupTool->id}");
            return response()->json($groupTool, 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on groupTool( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function update(Request $request,  $id)
    {
        $Auth=Auth::user();

        try {
            $groupTool= groupTool::find($id);
            if (!$groupTool) {
                throw new \Exception("GroupTool with id: {$id} dont exist", 500);
            }
            $validator = \Validator::make($request->all(),[
                'code'     => 'required|unique:group_tools,code,'.$groupTool->id,
                'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category_tools_id' =>'required|exists:category_tools,id',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            if ($request->file('image')) {
                $imagePath = $request->file('image');
                $imageName =  Str::of($imagePath->getClientOriginalName())->split('/[\s.]+/');
                $path = $request->file('image')->storeAs('images/groupTool/' . $groupTool->id,$groupTool->id."_profile.". $imageName[1], 'public');
                $groupTool->image=$path;
            }
            $groupTool->user_id=$Auth->id;
            $groupTool->update($request->all());
            Log::info("User with email {$Auth->email} updated groupTool number {$id} successfully");
            return response()->json($groupTool, 200);
        } catch (\Exception $exception) {
            Log::error("Try access update of groupTool with email {$Auth->email} but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

}
