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
            return response()->json(Inspection::paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  inspections but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }

    public function storeTool(Request $request)
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
            $inspection->updToolStatusTool($request->tool_id,$request->status);

            Log::info("User with email { $Auth->email} created inspection number { $inspection-->id}");
            return response()->json($inspection->load([]), 201);
        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on inspection( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function storeProjectTool(Request $request)
    {
        $Auth=Auth::user();
        try {

            $validator = \Validator::make($request->all(),[
                'additionalDescription' => 'required',
                'status' => 'required|boolean',
                'inspection_projecttool_id' => 'required|exists:inspection_projecttool,id'
            ]);


            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }


            if(!is_null(Inspection::inspectionProjectTool($request->inspection_projecttool_id, 'id')[0]->inspection_id))
                throw new \Exception("This inspection_projecttool already has a inspection", 500);

            $inspection= new Inspection();
            $inspection->user_id=$Auth->id;
            $inspection->status=$request->status;
            $inspection->additionalDescription=$request->additionalDescription;
            $inspection->save();

            Inspection::updInspectionId($request->inspection_projecttool_id, $inspection->id);
            Inspection::updProjectStatusTool($request->inspection_projecttool_id, $request->status);

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
            if($request->status != $inspection->status){

                $inspection->updStatusTool($inspection->getRelationShip(),$request->status);
                //$this->updatedStatusTool($inspection->inspectionTool(), $request->status);
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

    public function  indexProjectTool(){
        $Auth=Auth::user();

        try {

            Log::info("User with email {$Auth->email} get missing inspections  successfully");
            return response()->json(Inspection::missingInspections(), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  missing inspections  but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
    /**
     * Update status tool with status inspection
     *
     * @param QueryBuilder $data
     * @param Boolean $status
     * @return void
    */

    //public function updatedStatusTool(\Illuminate\Support\Collection $data, bool $status){
     //   if(!$data)
     //       return;
     //   $countData = count($data);
     //   for($i=0; $i < $countData; $i++){
      //      $tool = Tool::find($data[$i]->tool_id);
      //      $tool->status_tools_id = filter_var($status, FILTER_VALIDATE_BOOLEAN) ? 2 : 1;
      //      $tool->save();
     //   }
    //}
}
