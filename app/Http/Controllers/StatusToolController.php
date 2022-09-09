<?php

namespace App\Http\Controllers;

use App\StatusTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StatusToolController extends Controller
{

    public function index()
    {
        $Auth=Auth::user();

        try {
            Log::info("User with email {$Auth->email} get status successfully");
            return response()->json(StatusTool::with(['tools'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get status but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }


}
