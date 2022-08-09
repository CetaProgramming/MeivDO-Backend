<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'LoginController@login');
Route::post('/logout', 'LoginController@logout');

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/user', 'LoginController@user');
    Route::get('/test', function(Request $request){
        return response()->json('dsadas', 200);
    });
});