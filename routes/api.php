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
        Route::delete('/users/{id}', 'UserController@destroy');
        //GroupTools
        Route::get('/tools/groups', 'GroupToolController@index');
        Route::post('/tools/groups', 'GroupToolController@store');
        Route::put('/tools/groups/{id}', 'GroupToolController@update');
        Route::delete('/tools/groups/{id}', 'GroupToolController@destroy');
        //CategoryTools
        Route::get('/tools/category', 'CategoryToolController@index');
        Route::post('/tools/category', 'CategoryToolController@store');
    });
    Route::get('/roles','RoleController@index');
    Route::get('/user', 'LoginController@user');
    Route::put('/changePassword','UserController@updatePassword');

});

