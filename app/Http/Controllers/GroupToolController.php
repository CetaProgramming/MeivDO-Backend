<?php

namespace App\Http\Controllers;
use App\categoryTool;
use App\groupTool;
use App\User;
use App\Helpers\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\Active;
class GroupToolController extends Controller
{

    public function index()
    {
        $Auth=Auth::user();

        try {

            Log::info("User with email {$Auth->email} get groupTools successfully");
            return response()->json(GroupTool::with(['categoryTools','user'])->where('active',1)->paginate(15), 200);
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
                'image'       => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category' =>'required|exists:category_tools,id',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $groupTool= new groupTool();
            $groupTool->code=$request->code;
            $groupTool->category_tools_id=$request->category;
            Active::verifyActive(categoryTool::find($request->category));
            $groupTool->description=$request->description;
            $groupTool->active=1;
            $groupTool->user_id=$Auth->id;
            $groupTool->save();
            $request->image && $groupTool->image=ImageUpload::saveImage($request,"group_tools",$groupTool);
            $groupTool->save();
            Log::info("User with email { $Auth->email} created groupTool number { $groupTool->id}");
            return response()->json($groupTool::find($groupTool->id)->load(['categoryTools','user']), 201);
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
                'image'       => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category' =>'required|exists:category_tools,id',
                'active'=>'required|boolean',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }

            $groupTool->category_tools_id=$request->category;
            Active::verifyActive(categoryTool::find($request->category));
            $groupTool->user_id=$Auth->id;
            $groupTool->update($request->all());
            $request->image && $groupTool->image=ImageUpload::saveImage($request,"group_tools",$groupTool);
            $groupTool->save();
            Log::info("User with email {$Auth->email} updated groupTool number {$id} successfully");
            return response()->json($groupTool->load(['categoryTools','user']), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access update on groupTool but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $groupTool= groupTool::find($id);
            if (! $groupTool) {
                throw new \Exception("GroupTool with id: {$id} dont exist", 500);
            }
            $groupTool->delete();
            Log::info("User with email {$Auth->email} deleted groupTool number {$id}");
            Storage::deleteDirectory('public/images/groupTool/' .  $groupTool->id);
            return response()->json(['message' => 'Deleted'], 200);
        } catch (Exception $exception) {
            Log::error("User with email {$Auth->email} try access destroy  on groupTool but  is not possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

}
