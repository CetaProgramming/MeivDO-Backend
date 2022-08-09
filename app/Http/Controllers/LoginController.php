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

            $user = Auth::user();

            return response()->json($user, 200);  

        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode()) ;
        }        
    }

    public function user(Request $request){
        try {
            return $request;
            // return response()->json(, 200);  
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
            return response()->json(['error' => $exception], 500);
        }
    }

    public function logout(Request $request){
        try {
            Auth::logout();
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
