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
}
