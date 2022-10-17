<?php

namespace App\Http\Controllers;


use App\Inspection;
use  App\Tool;
use  App\StatusTool;
use App\ProjectTool;
use App\Inspection_Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;


class InspectionController extends Controller
{
    public function index()
    {
        $Auth=Auth::user();

        try {
            Log::info("User with email {$Auth->email} get inspections successfully");
            $inspections = Inspection::with(['user']);
            return response()->json(
                !$inspections->count() ?
                    $inspections->get()
                :
                tap($inspections->paginate(15),function($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function ($inspection) {
                        if($inspection->inspectionProjectTool($inspection->id, 'inspection_id')->count() >0 || $inspection->inspectionTool()->count()>0){
                            $inspection->getRelationToolOrProjectTool();
                        }
                        return $inspection;
                    });
                })
            ,200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  inspections but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }

     /**
     * Filter inspection completed by tool, status
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function searchCompletedInspections(Request $request){
        try {
            $Auth=Auth::user();

            $validator = \Validator::make($request->all(), [
                'tool_id' => 'nullable|integer|min:1',
                'status' => 'nullable|boolean'
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
    
            Log::info("User with email { $Auth->email} made a search by completed on table inspection");
            
            $inspections = Inspection::with(['user']);

            $collection = tap($inspections->paginate(15),function($paginatedInstance){
                return $paginatedInstance->getCollection()->transform(function ($inspection, $key) {
                    if($inspection->inspectionProjectTool($inspection->id, 'inspection_id')->count() >0 || $inspection->inspectionTool()->count()>0){
                        $inspection->getRelationToolOrProjectTool();
                    }
                    if(request()->tool_id && ($inspection->inspectionDetails['tool']->id != request()->tool_id))
                        return null;
                    if(!is_null(request()->status) && $inspection->status != request()->status)
                        return null;
                    return $inspection;
                });
            });

            $itemsTransformed = $collection->getCollection()->filter(function ($item) {
                if($item)
                    return $item;
              });
              
              $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsTransformed,
                $collection->total(),
                $collection->perPage(),
                $collection->currentPage(), [
                  'path' => \Request::url(),
                  'query' => [
                    'page' => $collection->currentPage()
                  ]
                ]
              );

            return response()->json(
                !$inspections->count() ?
                    $inspections->get()
                :
                $itemsTransformedAndPaginated
            ,200);

        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }        
    }

     /**
     * Filter inspection missing by tool, project
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function searchMissingInspections(Request $request){
        try {
            $Auth=Auth::user();

            $validator = \Validator::make($request->all(), [
                'tool_id' => 'nullable|integer|min:1',
                'project_id' => 'nullable|integer|min:1'
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
    
            Log::info("User with email { $Auth->email} made a search by completed on table inspection");
            
            $inspections = Inspection::missingInspections(false);

            $collection = tap($inspections->paginate(15),function($paginatedInstance){
                return $paginatedInstance->getCollection()->transform(function ($inspection) {
                    $projectTool = ProjectTool::find($inspection->project_tools_id);
                    $inspection->project = $projectTool->project;
                    $inspection->tool = $projectTool->tool;
                    if(request()->tool_id && $inspection->tool->id != request()->tool_id)
                        return null;
                    if(request()->project_id && $inspection->project->id != request()->project_id)
                        return null;
                    return $inspection;
                });
            });

            $itemsTransformed = $collection->getCollection()->filter(function ($item) {
                if($item)
                    return $item;
              });
              
              $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsTransformed,
                $collection->total(),
                $collection->perPage(),
                $collection->currentPage(), [
                  'path' => \Request::url(),
                  'query' => [
                    'page' => $collection->currentPage()
                  ]
                ]
              );

            return response()->json(
                !$inspections->count() ?
                    $inspections->get()
                :
                $itemsTransformedAndPaginated
            ,200);

        } catch (\Exception $exception) {
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
            $inspection->updToolStatusTool($request->tool_id,$request->status ? 2 : 1);
            $inspection->inspectionDetails = $inspection->getRelationShipTable();

            Log::info("User with email { $Auth->email} created inspection number { $inspection-->id}");
            return response()->json($inspection, 201);
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
            $tool_id = $inspection->GetInspectionToolId();
            if( $inspection->isLastInspection($tool_id)==true){
                if($request->status != $inspection->status){
                    $inspection->updStatusTool($inspection->getRelationShip(),$request->status ? 2 : 1);
                }
                $inspection->additionalDescription=$request->additionalDescription;
                $inspection->user_id=$Auth->id;
                $inspection->status=$request->status;
                $inspection->update($request->all());
            }else{
                throw new \Exception("Inspection with id: {$id} cannot be updated", 500);
            }

            $inspection->getRelationToolOrProjectTool();
            Log::info("User with email {$Auth->email} updated inspection number {$id} successfully");
            return response()->json($inspection, 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access update on inspection but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $inspection= Inspection::find($id);
            if (!$inspection) {
                throw new \Exception("Inspection with id: {$id} dont exist", 500);
            }

            if($inspection->validateDelete()){
                $inspection->delete();
            }else{
                throw new \Exception("Inspection with id: {$id} cannot be  deleted", 500);
            }
            
            Log::info("User with email {$Auth->email} deleted inspection number {$id}");
            return response()->json(['message' => 'Deleted'], 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access destroy  on  inspection but  is not possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function  indexProjectTool(){
        $Auth=Auth::user();

        try {
            Log::info("User with email {$Auth->email} get missing inspections  successfully");

            $inspections = Inspection::missingInspections(false);

            return response()->json(
                !$inspections->count() ?
                    $inspections->get()
                :
                tap($inspections->paginate(15),function($paginatedInstance){
                    return $paginatedInstance->getCollection()->transform(function ($inspection) {
                        $projectTool = ProjectTool::find($inspection->project_tools_id);
                        $inspection->project = $projectTool->project;
                        $inspection->tool = $projectTool->tool;
                        return $inspection;
                    });
                })
            , 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  missing inspections  but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
   // /**
    // * Update status tool with status inspection
    // *
    // * @param QueryBuilder $data
    // * @param Boolean $status
   //  * @return void
   // */
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
