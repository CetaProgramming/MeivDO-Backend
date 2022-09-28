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
    Route::middleware(['isAccess'])->group(function () {
        //Users
        Route::post('/users', 'UserController@store');
        Route::put('/users/{id}', 'UserController@update');
        Route::put('/users/resetPassword/{id}','UserController@resetPassword');
        Route::get('/users', 'UserController@index');
        Route::get('/users/search', 'UserController@searchData');
        Route::delete('/users/{id}', 'UserController@destroy');
        Route::get('/users/roles','RoleController@index');

        //GroupTools
        Route::get('/tools/groups', 'GroupToolController@index');
        Route::get('/tools/groups/search', 'GroupToolController@searchData');
        Route::post('/tools/groups', 'GroupToolController@store');
        Route::put('/tools/groups/{id}', 'GroupToolController@update');
        Route::delete('/tools/groups/{id}', 'GroupToolController@destroy');
        //CategoryTools
        Route::get('/tools/category', 'CategoryToolController@index');
        Route::post('/tools/category', 'CategoryToolController@store');
        Route::put('/tools/category/{id}', 'CategoryToolController@update');
        Route::delete('/tools/category/{id}', 'CategoryToolController@destroy');
        //StatusTool
        Route::get('/tools/status', 'StatusToolController@index');
        //Tool
        Route::get('/tools', 'ToolController@index');
        Route::get('/tools/search', 'ToolController@searchData');
        Route::post('/tools', 'ToolController@store');
        Route::put('/tools/{id}', 'ToolController@update');
        Route::delete('/tools/{id}', 'ToolController@destroy');
        //Project
        Route::get('/projects', 'ProjectController@index');
        Route::get('/projects/search', 'ProjectController@searchData');
        Route::post('/projects', 'ProjectController@store');
        Route::put('/projects/{id}', 'ProjectController@update');
        Route::delete('/projects/{id}', 'ProjectController@destroy');
        //ProjectTools
        Route::post('/projects/tools', 'ProjectToolController@store');
        //Route::put('/projects/tools/{id}', 'ProjectToolController@update');
        Route::delete('/projects/tools/{id}', 'ProjectToolController@destroy');
    });
    Route::get('/user', 'LoginController@user');
    Route::put('/changePassword','UserController@updatePassword');
    Route::put('/changeInfo','UserController@updateInfo');
});

