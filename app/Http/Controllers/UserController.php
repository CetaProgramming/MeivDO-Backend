<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{


    public function index()
    {
        $Auth=Auth::user();
        try {
            Log::info("User with email {$Auth->email} get users successfully");
            return response()->json(User::with(['role'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get users but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }

    }

    public function searchData(Request $request){

        $Auth=Auth::user();
        try {

            $validator = \Validator::make($request->all(), [
                'active' => 'nullable|boolean'
            ]);
            if ($validator->fails()) {
                $responseArr['message'] = $validator->errors()->first();
                return response()->json($responseArr, 500);
            }
            Log::info("User with email { $Auth->email} made a search on table users");
            return response()->json(User::where([
                    ["name", "LIKE", "%{$request->name}%"],
                    ["active", "LIKE", "%{$request->active}%"]
                ])
                ->orWhere([
                    ["email", "LIKE", "%{$request->name}%"],
                    ["active", "LIKE", "%{$request->active}%"]
                ])
                ->with(['role'])->paginate(), 200);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on search users( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function store(Request $request)
    {
        $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(),[
                'name'        => 'required',
                'email'     => 'required|unique:users,email,null,id,deleted_at,NULL|email:rfc,dns',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'role_id' => 'required|exists:roles,id|integer',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $user= new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password= bcrypt('12345');
            $user->active=1;
            $user->role_id=$request->role_id;
            $user->user_id=$Auth->id;
            $user->save();
            $request->image && $user->image=ImageUpload::saveImage($request,"users",$user);
            $user->save();
            Log::info("User with email { $Auth->email} created user number {$user->id}");
            return response()->json($user::find($user->id)->load(['role']), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on Users( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function update(Request $request,  $id)
    {
        $Auth=Auth::user();

        try {
           $user= User::find($id);
            if (!$user) {
                throw new \Exception("User with id: {$id} dont exist", 500);
            }
            $validator = \Validator::make($request->all(),[
                'name'        => 'required',
                'email'     => 'required|unique:users,email,'.$user->id.',id,deleted_at,NULL|email:rfc,dns',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'active'=>'required|boolean',
                'role_id' => 'required|exists:roles,id|integer',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $request->image && $user->image=ImageUpload::saveImage($request,"users",$user);
            $user->user_id=$Auth->id;
            $user->update($request->all());
            Log::info("User with email {$Auth->email} updated user number {$id} successfully");
            return response()->json($user->load(['role']), 200);
        } catch (\Exception $exception) {
            Log::error("Try access update of users with email {$Auth->email} but not is possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function updateInfo(Request $request)
    {
        $Auth=Auth::user();
        try {
            $user= User::find($Auth->id);
            $validator = \Validator::make($request->all(),[
                'name'        => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $request->image && $user->image=ImageUpload::saveImage($request,"users",$user);
            $user->user_id=$Auth->id;
            $user->update($request->all());
            Log::info("User with email {$Auth->email} updated their one info successfully");
            return response()->json($user->load(['role']), 200);
        } catch (\Exception $exception) {
            Log::error("Try access update their one info with email {$Auth->email} but not is possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function updatePassword(Request $request)
    {

        $Auth=Auth::user();

        try {

            $user= User::find($Auth->id);

            $validator = \Validator::make($request->all(),[
                'password'        => 'required',
                'newPassword'     => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }

            if(!Hash::check($request->password,$user->password)){
                throw new \Exception("Password dont match", 500);
            }

            $user->password =bcrypt($request->newPassword);
            $user->validate=1;
            $user->user_id=$Auth->id;
            $user->save();
            Log::info("User with email {$Auth->email} change is password successfully");
            return response()->json($user->load(['role']), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try to change is password but not is possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function resetPassword(Request $request,$id)
    {

        $Auth=Auth::user();

        try {
                $user= User::find($id);
                if (!$user) {
                    throw new \Exception("User with id: {$id} dont exist", 500);
                }

            $user->password =bcrypt('12345');
            $user->validate=0;
            $user->user_id=$Auth->id;
            $user->save();
            Log::info("User with email {$Auth->email} reset the password of user with id {$id} successfully");
            return response()->json($user->load(['role']), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try to reset the password of user with id {$id} but not is possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $user= User::find($id);
            if($Auth->id ==$id){
                throw new \Exception("You cant delete user with id: {$id} because its yourself", 500);
            }
            if (!$user) {
                throw new \Exception("User with id: {$id} dont exist", 500);
            }
                $user->delete();
                Log::info("User with email {$Auth->email} deleted user number {$id}");
                Storage::deleteDirectory('public/images/users/' . $user->id);
                return response()->json(['message' => 'Deleted'], 200);



        } catch (\Exception $exception) {
            Log::error("Try access destroy of users with email {$Auth->email} but not is possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
