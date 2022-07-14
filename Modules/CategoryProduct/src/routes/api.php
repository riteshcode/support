<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\HRM\Http\Controllers\DepartmentController;
use Modules\HRM\Http\Controllers\LeaveController;
use Modules\HRM\Http\Controllers\LeaveTypeController;
use Modules\HRM\Http\Controllers\AttendanceController;



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

    Route::group(['prefix' => 'department'], function() {
        Route::post('/all', [DepartmentController::class,'index_all']);
        Route::post('/', [DepartmentController::class,'index']);
        Route::post('/store', [DepartmentController::class,'store']);
        Route::post('/edit', [DepartmentController::class,'edit']);
        Route::post('/update', [DepartmentController::class,'update']);
        Route::post('/delete', [DepartmentController::class,'destroy']);
    });

    Route::group(['prefix' => 'leaveType'], function() {
        Route::post('/', [LeaveTypeController::class,'index']);
        Route::post('/store', [LeaveTypeController::class,'store']);
        Route::post('/edit', [LeaveTypeController::class,'edit']);
        Route::post('/update', [LeaveTypeController::class,'update']);
        Route::post('/delete', [LeaveTypeController::class,'destroy']);
    });

    Route::group(['prefix' => 'leave'], function() {
        Route::post('/', [LeaveController::class,'index']);
        Route::post('/store', [LeaveController::class,'store']);
        Route::post('/edit', [LeaveController::class,'edit']);
        Route::post('/update', [LeaveController::class,'update']);
        Route::post('/status', [LeaveController::class,'statusChange']);
    });

    Route::group(['prefix' => 'attendance'], function() {
        Route::post('/', [AttendanceController::class,'index']);
        Route::post('/check-in', [AttendanceController::class,'checkIn']);
        Route::post('/check-out', [AttendanceController::class,'checkOut']);
    });
    
});

