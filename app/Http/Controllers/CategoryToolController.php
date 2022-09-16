<?php

namespace App\Http\Controllers;

use App\categoryTool;
use App\groupTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryToolController extends Controller
{
    public function index()
    {
        $Auth=Auth::user();

        try {

            Log::info("User with email {$Auth->email} get categoryTools successfully");
            return response()->json(categoryTool::with(['groupTools','user'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get categoryTools but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
    public function store(Request $request)
    {
        $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(),[
                'name' => 'required|unique:category_tools',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $categoryTool= new categoryTool();
            $categoryTool->user_id=$Auth->id;
            $categoryTool->active=1;
            $categoryTool->name=$request->name;
            $categoryTool->save();
            Log::info("User with email { $Auth->email} created categoryTool number { $categoryTool->id}");
            return response()->json($categoryTool->load(['groupTools','user']), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on categoryTool( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function update(Request $request,  $id)
    {
        $Auth=Auth::user();

        try {
            $categoryTool= categoryTool::find($id);
            if (!$categoryTool) {
                throw new \Exception("CategoryTool with id: {$id} dont exist", 500);
            }
            $validator = \Validator::make($request->all(),[
                'name' => 'required|unique:category_tools,name,'.$categoryTool->id,
                'active'=>'required|boolean',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $categoryTool->user_id=$Auth->id;
            $categoryTool->update($request->all());
            Log::info("User with email {$Auth->email} updated categoryTool number {$id} successfully");
            return response()->json($categoryTool->load(['groupTools','user']), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access update on categoryTool but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $categoryTool= categoryTool::find($id);
            if (!$categoryTool) {
                throw new \Exception("CategoryTool with id: {$id} dont exist", 500);
            }
            $categoryTool->delete();
            Log::info("User with email {$Auth->email} deleted categoryTool number {$id}");
            return response()->json(['message' => 'Deleted'], 200);
        } catch (Exception $exception) {
            Log::error("User with email {$Auth->email} try access destroy  on categoryTool but  is not possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
