<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CartsController;
use App\Http\Controllers\TransactionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/petshop/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/petshop/profile-edit/post', [AuthController::class, 'update']);
    Route::post('/petshop/logout', [AuthController::class, 'logout']);

    Route::controller(CategoriesController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::post('/category/post', 'store');
        Route::post('/category/update/{id}', 'update');
        Route::get('/category/delete/{id}', 'destroy');
    });

    Route::controller(UnitsController::class)->group(function () {
        Route::get('/units', 'index');
        Route::post('/unit/post', 'store');
        Route::post('/unit/update/{id}', 'update');
        Route::get('/unit/delete/{id}', 'destroy');
    });

    Route::controller(ProductsController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/product/details/{id}', 'show');
        Route::post('/product/post', 'store');
        Route::post('/product/update/{id}', 'update');
        Route::get('/product/delete/{id}', 'destroy');
    });

    Route::controller(CartsController::class)->group(function () {
        Route::post('/cart/post', 'store');
        Route::post('/cart/update', 'update');
        Route::post('/cart/delete', 'destroy');
    });

    Route::controller(TransactionsController::class)->group(function () {
        Route::get('/transactions', 'index');
        Route::get('/transaction/details/{transaction_id}', 'details');
        Route::get('/transactions/pending', 'show');
        Route::post('/transactions/checkout', 'store');
    });
});
Route::middleware('auth:api')->group(function () {
    Route::controller(TransactionsController::class)->group(function () {
        Route::get('/transactions', 'index');
        Route::get('/transaction/details/{transaction_id}', 'details');
        Route::get('/transactions/pending', 'show');
        Route::post('/transactions/checkout', 'store');
    });
});
