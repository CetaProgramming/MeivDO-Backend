<?php

namespace App\Http\Controllers;

use App\ProjectTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectToolController extends Controller
{
    public function destroy($id)
    {
        $Auth =Auth::user();
        try {
            $projectTool= projectTool::find($id);
            if (!$projectTool) {
                throw new \Exception("Project with id: {$id} dont exist", 500);
            }
            $projectTool->delete();
            Log::info("User with email {$Auth->email} deleted project tool number {$id}");
            return response()->json(['message' => 'Deleted'], 200);
        } catch (Exception $exception) {
            Log::error("User with email {$Auth->email} try access destroy  on project tool but  is not possible!Message error({$exception->getMessage()})");
            return response()->json(['error' => $exception->getMessage()->errors()->first()], $exception->getCode());
        }
    }
}
