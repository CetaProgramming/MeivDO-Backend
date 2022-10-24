<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        $Auth=Auth::user();

        try {
           Log::info("User with email { $Auth->email} get roles successfully");
            return response()->json(Role::all(), 200);
        } catch (\Exception $exception) {
            Log::error("Try access with email { $Auth->email} try get roles but not successfully!");
            return response()->json(['error' => $exception], 500);
        }
    }
}
