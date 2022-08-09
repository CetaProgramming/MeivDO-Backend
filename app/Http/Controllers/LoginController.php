<?php

namespace App\Http\Controllers;

use App\Login;
use Illuminate\Http\Request;
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

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => 1]))
                throw new \Exception('Invalid credentials', 401);
                
            $request->session()->regenerate();

            return response()->json(Auth::user()->data(), 200);  

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode()) ;
        }        
    }

    public function user(Request $request){
        try {
            return response()->json(Auth::user()->data(), 200);  
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function logout(Request $request){
        try {
            Auth::logout();
            return response()->json(['message' => "See you soon, come back when you need"], 200);  
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }
}
