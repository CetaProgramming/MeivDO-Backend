<?php

namespace App\Http\Controllers;

use App\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ToolController extends Controller
{

    public function index()
    {
        $Auth=Auth::user();

        try {
            Log::info("User with email {$Auth->email} get groupTools successfully");
            return response()->json(Tool::with(['statusTools','groupTools','user'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get groupTools but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }

}
