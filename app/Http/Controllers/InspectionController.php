<?php

namespace App\Http\Controllers;


use App\Inspection;
use  App\Tool;
use  App\StatusTool;
use App\Inspection_Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InspectionController extends Controller
{
    public function index()
    {
        $Auth=Auth::user();

        try {

            Log::info("User with email {$Auth->email} get inspections successfully");
            return response()->json(Inspection::with([])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  inspections but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
    public function store(Request $request)
    {
        $Auth=Auth::user();
        try {
            $validator = \Validator::make($request->all(),[
                'additionalDescription' => 'required',
                'status' => 'required|boolean',
                'tool_id' => 'required|exists:tools,id,deleted_at,NULL,active,1'
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $tool = Tool::find($request->tool_id);
            $statusTool = StatusTool::find(2);
            if($tool->status_tools_id != 2 ){
                throw new \Exception("The tool must have the status in {$statusTool->name}", 500);
            }
            $inspection= new Inspection();
            $inspection->user_id=$Auth->id;
            $inspection->status=$request->status;
            $inspection->additionalDescription=$request->additionalDescription;
            $inspection->save();
            $inspectionTool= new Inspection_Tool();
            $inspectionTool->inspection_id= $inspection->id;
            $inspectionTool->tool_id=$request->tool_id;

            $inspectionTool->save();

            if($request->status == 1){
               $tool->status_tools_id=2;
            }else{
                $tool->status_tools_id=1;
            }
            $tool->save();
            Log::info("User with email { $Auth->email} created inspection number { $inspection-->id}");
            return response()->json($inspection->load([]), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on inspection( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function update(Request $request,  $id)
    {
        $Auth=Auth::user();

        try {
            $inspection= Inspection::find($id);
            if (!$inspection) {
                throw new \Exception("Inspection with id: {$id} dont exist", 500);
            }
            $validator = \Validator::make($request->all(),[
                'additionalDescription' => 'required',
                'status' => 'required|boolean',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $inspectionTool = Inspection_Tool::where('inspection_id',$inspection->id)->get();
            dd($inspectionTool);
          //  dd($inspection->inspectionTool);
            if( $request->status != $inspection->status){


                if($inspectionTool !=null){
                    dd($inspectionTool);
                    $tool = Tool::find($inspectionTool->tool_id);

                    if($request->status == 1){
                        $tool->status_tools_id=2;
                    }else{
                        $tool->status_tools_id=1;
                    }
                    $tool->save();

                }else{
                    dd("não entrou");
                }
            }
            $inspection->additionalDescription=$request->additionalDescription;
            $inspection->user_id=$Auth->id;
            $inspection->status=$request->status;
            $inspection->update($request->all());

            Log::info("User with email {$Auth->email} updated inspection number {$id} successfully");
            return response()->json($inspection->load([]), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access update on inspection but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
