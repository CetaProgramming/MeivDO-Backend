<?php

namespace App\Http\Controllers;

use App\Inspection_Tool;
use App\StatusTool;
use App\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InspectionToolController extends Controller
{
   // public function store(Request $request)
  //  {
     //   $Auth=Auth::user();
     //   try {
//
      //      $validator = \Validator::make($request->all(),[
       //         'inspection_id' => 'required|unique:inspection_tools,inspection_id,null,id,deleted_at,NULL|exists:inspection,id,deleted_at,NULL',
        //        'tool_id' => 'required|exists:tools,id,deleted_at,NULL,active,1',
        //    ]);
          //  if ($validator->fails()) {
         //       throw new \Exception($validator->errors()->first(), 500);
         //   }
         //   $tool = tool::find($request->tool_id);
          //  $statusTool = StatusTool::find(2);
          //  if($tool->status_tools_id != 2 ){
         //       throw new \Exception("The tool must have the status in {$statusTool->name}", 500);
         //   }
       //     $inspectionTool= new Inspection_Tool();
         //   $inspectionTool->inspection_id=$request->inspection_id;
        //    $inspectionTool->tool_id=$request->tool_id;
          //  $inspectionTool->save();
          //  Log::info("User with email { $Auth->email} created inspectionTool number { $inspectionTool->id}");
          //  return response()->json($inspectionTool->load([]), 201);
       // } catch (\Exception $exception) {
      //      Log::error("User with email { $Auth->email} receive an error on  inspectionTool ( {$exception->getMessage()})");
     //       return response()->json(['error' => $exception->getMessage()], $exception->getCode());
     //   }
   // }
}
