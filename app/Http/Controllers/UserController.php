<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class UserController extends Controller
{
    public function index()
    {
        $request =Auth::user();
        try {
            //Log::info("User with email {$request->email} started a new session");
            return response()->json(DB::table('users')->paginate(15), 200);
        } catch (\Exception $exception) {
            //Log::error("Try access with email {$request->email} but not is possible!");
            return response()->json(['error' => $exception], 500);
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
        try {

            $User = DB::table('users')->where('id', $id );

            if ($User->exists()) {
                $User->update($request->all());
                Log::info("User with email {$request->email} updated user number {$id}");
                return response()->json($User->paginate(1), 200);
            }
            else{
                Log::error("User with email {$request->email} try update user number {$id} but was not possible!");
                return response()->json(['error' => "User with id: {$id} dont exist"], 500);
            }
        } catch (Exception $exception) {
            Log::error("Try access update of users with email {$request->email} but not is possible!");
            return response()->json(['error' => $exception], 500);
        }
    }


    public function destroy(Bicycle $bicycle)
    {
        try {
            $bicycle->delete();
            return response()->json(['message' => 'Deleted'], 205);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }
}
