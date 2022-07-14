<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\ProductController;
use Modules\Ecommerce\Http\Controllers\BrandController;
use Modules\Ecommerce\Http\Controllers\CategoryController;
use Modules\Ecommerce\Http\Controllers\FieldsGroupController;
use Modules\Ecommerce\Http\Controllers\ProductTypeController;
use Modules\Ecommerce\Http\Controllers\FieldController;
use Modules\Ecommerce\Http\Controllers\ProductsOptionsValuesController;
use Modules\Ecommerce\Http\Controllers\SupplierController;



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

    Route::group(['prefix' => 'product'], function() {
        Route::post('/', [ProductController::class,'index']);
        Route::post('/create', [ProductController::class,'create']);
        Route::post('/store', [ProductController::class,'store']);
        Route::post('/edit', [ProductController::class,'edit']);
        Route::post('/update', [ProductController::class,'update']);
        Route::post('/changeStatus', [ProductController::class,'changeStatus']);
    });
    
    Route::group(['prefix' => 'brand'], function() {
        Route::post('/', [BrandController::class,'index']);
        Route::post('/store', [BrandController::class,'store']);
        Route::post('/edit', [BrandController::class,'edit']);
        Route::post('/update', [BrandController::class,'update']);
        Route::post('/delete', [BrandController::class,'destroy']);
    });

    Route::group(['prefix' => 'categories'], function() {
        Route::post('/create_sub_category', [CategoryController::class,'create_sub_category']);
        Route::post('/', [CategoryController::class,'index']);
        Route::post('/store', [CategoryController::class,'store']);
        Route::post('/edit', [CategoryController::class,'edit']);
        Route::post('/update', [CategoryController::class,'update']);
        Route::post('/changeStatus', [CategoryController::class,'changeStatus']);
    });

    Route::group(['prefix' => 'fieldsgroup'], function() {
        Route::post('/', [FieldsGroupController::class,'index']);
        Route::post('/store', [FieldsGroupController::class,'store']);
        Route::post('/edit', [FieldsGroupController::class,'edit']);
        Route::post('/update', [FieldsGroupController::class,'update']);
        Route::post('/changeStatus', [FieldsGroupController::class,'changeStatus']);
    });

    Route::group(['prefix' => 'field'], function() {
        Route::post('/', [FieldController::class,'index']);
        Route::post('/store', [FieldController::class,'store']);
        Route::post('/edit', [FieldController::class,'edit']);
        Route::post('/update', [FieldController::class,'update']);
        Route::post('/changeStatus', [FieldController::class,'changeStatus']);
    });
    
    Route::group(['prefix' => 'product_type'], function() {
        Route::post('/', [ProductTypeController::class,'index']);
        Route::post('/store', [ProductTypeController::class,'store']);
        Route::post('/edit', [ProductTypeController::class,'edit']);
        Route::post('/update', [ProductTypeController::class,'update']);
        Route::post('/changeStatus', [ProductTypeController::class,'changeStatus']);
    });

       
    Route::group(['prefix' => 'product_options'], function() {
        Route::post('/', [ProductOptionsController::class,'index']);
        Route::post('/store', [ProductOptionsController::class,'store']);
        Route::post('/edit', [ProductOptionsController::class,'edit']);
        Route::post('/update', [ProductOptionsController::class,'update']);
        Route::post('/changeStatus', [ProductOptionsController::class,'changeStatus']);
    });

       
    Route::group(['prefix' => 'product_options_value'], function() {
        Route::post('/', [ProductsOptionsValuesController::class,'index']);
        Route::post('/store', [ProductsOptionsValuesController::class,'store']);
        Route::post('/edit', [ProductsOptionsValuesController::class,'edit']);
        Route::post('/update', [ProductsOptionsValuesController::class,'update']);
        Route::post('/changeStatus', [ProductsOptionsValuesController::class,'changeStatus']);
    });
    
    Route::group(['prefix' => 'supplier'], function() {
        Route::post('/', [SupplierController::class,'index']);
        Route::post('/store', [SupplierController::class,'store']);
        Route::post('/edit', [SupplierController::class,'edit']);
        Route::post('/update', [SupplierController::class,'update']);
        Route::post('/changeStatus', [SupplierController::class,'changeStatus']);
    });

});

