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
            $validator = \Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);
    
            if ($validator->fails()) {
                $responseArr['message'] = $validator->errors()->first();
                return response()->json($responseArr, 500);
            }

            $userLogin = $this->verifUser($request);

            return response()->json([
                "access_token" => $userLogin->createToken('access_aplication')->plainTextToken,
                "type" => "Bearer"
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode()) ;
        }        
    }

    public function user(Request $request){
        try {
            $user = $request->user();
            return response()->json($user->data(), 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
            return response()->json(['error' => $exception], 500);
        }
    }

    public function logout(Request $request){
        try {
            $userFind = $this->findUserByEmail($request->user()->email);

            $userFind->deleteToken($request);

            return response()->json(["message" => "{$userFind->name}, See you later! Come back when you need!"], 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    private function verifUser(Request $request){

        if (!Auth::attempt($request->only(['email', 'password']))) {
            abort(403);
        }
        // if((!Auth::attempt($request->only(['email', 'password']))))
        //     throw new \Exception('User not found, please check your email address and password!', 404);
        $user = $this->findUserByEmail($request->email);
        if(!$user->active)
            throw new \Exception('User is disabled, please contact administrator or your manager!', 404);
        return $user; 
    }

    private function findUserByEmail($email){
        try {
            $userFound = Login::where('email', $email)->first();
            if(!$userFound)
                throw new \Exception('User not found, please check your email address!', 404);            
            return $userFound;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
