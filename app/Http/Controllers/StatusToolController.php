<?php

namespace App\Http\Controllers;

use App\StatusTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StatusToolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Auth=Auth::user();

        try {

            Log::info("User with email {$Auth->email} get status successfully");
            return response()->json(StatusTool::paginate(15), 200);
        } catch (\Exception $exception) {
            Log::error("User with email {$Auth->email} try get status but not successfully!");
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StatusTool  $statusTool
     * @return \Illuminate\Http\Response
     */
    public function show(StatusTool $statusTool)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StatusTool  $statusTool
     * @return \Illuminate\Http\Response
     */
    public function edit(StatusTool $statusTool)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StatusTool  $statusTool
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StatusTool $statusTool)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StatusTool  $statusTool
     * @return \Illuminate\Http\Response
     */
    public function destroy(StatusTool $statusTool)
    {
        //
    }
}
