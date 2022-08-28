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
Route::get('/logout', 'LoginController@logout');

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/user', 'LoginController@user');
    Route::get('/user', 'LoginController@user');
    Route::delete('/users/{id}', 'UserController@destroy');
    Route::put('/users/{id}', 'UserController@update');
    Route::get('/users', 'UserController@index');
    Route::get('/roles','RoleController@index');
});
