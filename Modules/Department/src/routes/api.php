<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Department\Http\Controllers\RolesController;
use Modules\Department\Http\Controllers\PermissionController;


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

Route::group(['middleware' => ['ApiTokenCheck'],'prefix'=>"api/"], function() {

    Route::post('roles', [RolesController::class,'index']);
    Route::post('role/store', [RolesController::class,'store']);
    Route::post('role/edit', [RolesController::class,'edit']);
    Route::post('role/update', [RolesController::class,'update']);
    Route::post('role/delete', [RolesController::class,'destroy']);
    Route::post('rolesAll', [RolesController::class,'roles_all']);    
    Route::post('permissions', [PermissionController::class,'index']);

});

