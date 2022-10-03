<?php

namespace App\Http\Controllers;


use App\Inspection;
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
}
