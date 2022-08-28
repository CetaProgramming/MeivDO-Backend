<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function index()
    {
        $request =Auth::user();
        try {
            Log::info("User with email {$request->email} started a new session");
            return response()->json(DB::table('users')->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("Try access with email {$request->email} but not is possible!");
            return response()->json(['error' => $exception], 500);
        }

    }
    public function store(Request $request)
    {
        try {
            $user = User::create($request->all());
            return response()->json($user, 201);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception], 500);
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


    public function destroy($id)
    {
        $request =Auth::user();
        try {
            $User = DB::table('users')->where('id', $id );
            if ($User->exists()) {
                $User->delete();
                // Log::info("User with email {$request->email} deleted user number {$id}");
                return response()->json(['message' => 'Deleted'], 200);
            }
            else{
                // Log::error("User with email {$request->email} try to delete user number {$id} but was not possible!");
                return response()->json(['error' => "User with id: {$id} dont exist"], 500);
            }
        } catch (Exception $exception) {
            return response()->json(['error' => $exception], 500);
        }
    }
}
