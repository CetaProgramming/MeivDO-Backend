<?php

namespace App\Http\Controllers;

use App\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => 1])){
                Log::error("Try access with email {$request->email} but not is possible!");
                throw new \Exception('Invalid credentials', 401);
            }

            $request->session()->regenerate();

            Log::info("User with email {$request->email} started a new session");

            return response()->json(Auth::user()->data(), 200);  

        } catch (\Exception $exception) {
            Log::error("Occurred exception: {$exception->getMessage()}");
            return response()->json(['error' => 'Something went wrong, sorry!'], 500);
        }        
    }

    public function user(Request $request){
        try {
            Log::info("User ".Auth::id()." acess your information!");
            return response()->json(Auth::user()->data(), 200);  
        } catch (\Exception $exception) {
            Log::error("Occurred exception: {$exception->getMessage()}");
            return response()->json(['error' => 'Something went wrong, sorry!'], 500);
        }
    }

    public function logout(Request $request){
        try {
            if(!Auth::id())
                throw new \Exception("First, you have to log in!", 401);
            Log::info("User ".Auth::id()." do logout");
            Auth::logout();
            return response()->json(['message' => "See you soon, come back when you need"], 200);  
        } catch (\Exception $exception) {
            Log::error("Occurred exception: {$exception->getMessage()}");
            return response()->json(['error' => 'Something went wrong, sorry!'], 500);
        }
    }
}
