<?php

namespace App\Http\Controllers;

use App\categoryTool;
use App\groupTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryToolController extends Controller
{
    public function store(Request $request)
    {
        $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(),[
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $categoryTool= new categoryTool();
            $categoryTool->name=$request->name;
            $categoryTool->save();
            Log::info("User with email { $Auth->email} created categoryTool number { $categoryTool->id}");
            return response()->json($categoryTool, 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on categoryTool( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
