<?php

namespace App\Http\Controllers;


use App\Inspection;
use App\Reparation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReparationController extends Controller
{

    public function indexRepairsCompleted()
    {
        $Auth=Auth::user();
        try {
            Log::info("User with email {$Auth->email} get reparations successfully");
            $reparations=
            tap(Reparation::where('status',1)->with(['inspection'])->paginate(15),function($paginatedInstance) {
                return $paginatedInstance->getCollection()->transform(function ($reparation) {
                    $inspection= Inspection::find($reparation->inspection_id);
                    $reparation->inspection->tool =$inspection->getRelationShipTable()['tool'];
                    return $reparation;
                });
            });
            return response()->json($reparations,200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  reparations but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function searchRepairsCompleted(Request $request){
        try {
            $Auth=Auth::user();

            $validator = \Validator::make($request->all(), [
                'tool_id' => 'nullable|integer|min:1',
                'inspection_id' => 'nullable|integer|min:1'
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            Log::info("User with email { $Auth->email} made a search on table missingRepairsCompleted");
            return response()->json(Reparation::where([
                ["tool_id", "=", $request->tool_id],
                ["inspection_id", "=", $request->inspection_id]
            ])->with(['inspection'])->paginate(), 200);
            } catch (\Exception $exception) {
                Log::error("User with email { $Auth->email} receive an error on search missingRepairsCompleted( {$exception->getMessage()})");
                return response()->json(['error' => $exception->getMessage()], $exception->getCode());
            }
    }
    public function searchRepairsMissing(Request $request){
        try {
            $Auth=Auth::user();

            $validator = \Validator::make($request->all(), [
                'tool_id' => 'nullable|integer|min:1',
                'inspection_id' => 'nullable|integer|min:1'
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            Log::info("User with email { $Auth->email} made a search on table missingRepairsCompleted");
            $collection=
                tap(Reparation::where('status',0)->with(['inspection'])->paginate(15),function($paginatedInstance) {
                    return $paginatedInstance->getCollection()->transform(function ($reparation) {
                        $inspection= Inspection::find($reparation->inspection_id);
                        $reparation->inspection->tool =$inspection->getRelationShipTable()['tool'];
                        if(request()->tool_id && $reparation->inspection->tool->id != request()->tool_id){
                            return  null;
                        }
                        if(request()->inspection_id && $reparation->inspection_id != request()->inspection_id){
                            return null;
                        }
                        return $reparation;
                    });
                });
            $itemsTransformed = $collection->getCollection()->filter(function ($item) {
                if($item)
                    return $item;
            });

            $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsTransformed->values(),
                $collection->total(),
                $collection->perPage(),
                $collection->currentPage(), [
                    'path' => \Request::url(),
                    'query' => [
                        'page' => $collection->currentPage()
                    ]
                ]
            );

            return response()->json(   $itemsTransformedAndPaginated, 200);

        } catch (\Exception $exception) {
            Log::error("User with email { $Auth->email} receive an error on search missingRepairsCompleted( {$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function indexRepairsMissing()
    {

        $Auth = Auth::user();

        try {
            Log::info("User with email {$Auth->email} get reparations successfully");
            $reparations=
                tap(Reparation::where('status',0)->with(['inspection'])->paginate(15),function($paginatedInstance) {
                    return $paginatedInstance->getCollection()->transform(function ($reparation) {
                        $inspection= Inspection::find($reparation->inspection_id);
                        $reparation->inspection->tool =$inspection->getRelationShipTable()['tool'];
                        return $reparation;
                    });
                });
            return response()->json($reparations, 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  reparations but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function update(Request $request,  $id)
    {
        $Auth=Auth::user();

        try {
            $reparation= Reparation::find($id);
            if (!$reparation) {
                throw new \Exception("Reparation with id: {$id} dont exist", 500);
            }
            $validator = \Validator::make($request->all(),[
                'reason' => 'required|string',
                'solution'=>'required|string',
                'additionalDescription'=>'required|string',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), 500);
            }
            $reparation->status =1;
            $reparation->user_id=$Auth->id;
            $reparation->update($request->all());
            Log::info("User with email {$Auth->email} updated reparation number {$id} successfully");
            $inspection= Inspection::find($reparation->inspection_id);
            $reparation->inspection->tool =$inspection->getRelationShipTable()['tool'];

            return response()->json($reparation,200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access update on reparation but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public  function  updateReset($id){
        $Auth=Auth::user();

        try {
            $reparation= Reparation::find($id);
            if (!$reparation) {
                throw new \Exception("Reparation with id: {$id} dont exist", 500);
            }
            $inspectionId =$reparation->inspection_id;
            $reparation->user_id=$Auth->id;
            $reparation->createReparation($Auth,$inspectionId);
            $inspection= Inspection::find($reparation->inspection_id);
            $reparation->inspection->tool =$inspection->getRelationShipTable()['tool'];
            Log::info("User with email {$Auth->email} reseted reparation number {$id} successfully");
            return response()->json($reparation, 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access resete on reparation but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
