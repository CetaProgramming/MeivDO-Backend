<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
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
            $validator = \Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);
    
            if ($validator->fails()) {
                $responseArr['message'] = $validator->errors()->first();
                return response()->json($responseArr, 500);
            }
            $user = $this->verifUser($request);


            return response()->json($user, 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode()) ;
        }        
    }

    private function verifUser(Request $request){
        if((!Auth::attempt($request->only(['email', 'password']))))
            throw new \Exception('User not found, please verify your email address and password!', 404);
        $user = User::where('email', $request->email)->first();
        if(!$user->active)
            throw new \Exception('User is disabled, please contact administrator or your manager!', 404);
        return $user; 
    }
}
