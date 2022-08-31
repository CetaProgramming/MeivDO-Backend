<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class UserController extends Controller
{


    public function index()
    {
        $Auth=Auth::user();
        try {

            Log::info("User with email {$Auth->email} get users successfully");
            return response()->json(User::paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get users but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }

    }
    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(),[
                'name'        => 'required',
                'email'     => 'required|unique:users|email:rfc,dns',
                'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'role_id' => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $user= new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password= bcrypt($request->name.'123');

            $user->active=1;
            //$user->user_id =Auth::user()->id;
            $user->role_id=$request->role_id;
            $user->save();
            if ($request->file('image')) {
                $imagePath = $request->file('image');
                $imageName =  Str::of($imagePath->getClientOriginalName())->split('/[\s.]+/');
                $path = $request->file('image')->storeAs('images/users/' . $user->id,$user->id."_profile.". $imageName[1], 'public');
                $user->image=$path;
            }
            $user->save();
           // Log::info("User with email {"$request->email"} created user number {$user->id}");
            return response()->json($user, 201);
        } catch (\Exception $exception) {
            Log::error("User with email {$request->email} receive an error on Users( {$exception->getMessage()})");
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
                'email'     => 'required|unique:users,email,'.$user->id,
                'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'role_id' => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }

            if ($request->file('image')) {
                $imagePath = $request->file('image');
                $imageName =  Str::of($imagePath->getClientOriginalName())->split('/[\s.]+/');
                $path = $request->file('image')->store('images/users/' . $user->id,$user->id."_profile.". $imageName[1], 'public');
                $user->image=$path;
            }
            $user->update($request->all());
                Log::info("User with email {$Auth->email} updated user number {$id} successfully");
                return response()->json($user, 200);
        } catch (\Exception $exception) {
            Log::error("Try access update of users with email {$Auth->email} but not is possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }


    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $user= User::find($id);
            if (!$user) {
                throw new \Exception("User with id: {$id} dont exist", 500);
            }
                $user->delete();
                Log::info("User with email {$request->email} deleted user number {$id}");
                Storage::deleteDirectory('public/images/users/' . $user->id);
                return response()->json(['message' => 'Deleted'], 200);



        } catch (Exception $exception) {
            Log::error("Try access destroy of users with email {$Auth->email} but not is possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
