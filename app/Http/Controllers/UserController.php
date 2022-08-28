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
}
