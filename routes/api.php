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
    Route::controller(AuthController::class)->prefix('profile')->group(function () {
        Route::get('', 'me');
        Route::post('/edit/post', 'update');
        Route::post('/logout', 'logout');
    });

    Route::get('/categories', [CategoriesController::class, 'index']);
    Route::get('/units', [UnitsController::class, 'index']);

    Route::controller(ProductsController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/product/details/{id}', 'show');
    });

    Route::controller(CartsController::class)->group(function () {
        Route::post('/cart/post', 'store');
        Route::post('/cart/update', 'update');
        Route::post('/cart/delete', 'destroy');
    });

    Route::controller(TransactionsController::class)->group(function () {
        Route::get('/transactions', 'index');
        Route::get('/transaction/details/{transaction_id}', 'details');
        Route::get('/transactions/pending', 'pending');
        Route::post('/transactions/checkout', 'store');
    });
});
