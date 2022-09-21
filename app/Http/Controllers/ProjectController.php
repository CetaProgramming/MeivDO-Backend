<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        $Auth=Auth::user();

        try {

            Log::info("User with email {$Auth->email} get projects successfully");
            return response()->json(Project::with(['projectTools','user'])->paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get projects but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }
}
