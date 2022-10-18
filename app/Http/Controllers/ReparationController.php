<?php

namespace App\Http\Controllers;


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
            $reparations = Reparation::where('status',1)->with(['inspection'])->paginate(15);
            return response()->json($reparations,200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get  reparations but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
    public function indexRepairsMissing()
    {

        $Auth = Auth::user();

        try {
            Log::info("User with email {$Auth->email} get reparations successfully");
            $reparations = Reparation::where('status', 0)->with(['inspection'])->paginate(15);
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
            return response()->json($reparation->load(['inspection']), 200);
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
            Log::info("User with email {$Auth->email} reseted reparation number {$id} successfully");
            return response()->json($reparation->load(['inspection']), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try access resete on reparation but is not possible!Message error({$exception->getMessage()}");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
