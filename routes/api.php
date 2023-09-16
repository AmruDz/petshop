<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/login', [AuthController::class, 'login']);

Route::controller(CategoriesController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::post('/categories/post', 'store');
    Route::get('/categories/details/{id}', 'show');
    Route::post('/categories/update/{id}', 'update');
    Route::get('/categories/delete/{id}', 'destroy');
});

Route::controller(UnitsController::class)->group(function () {
    Route::get('/units', 'index');
    Route::post('/units/post', 'store');
    Route::get('/units/details/{id}', 'show');
    Route::post('/units/update/{id}', 'update');
    Route::get('/units/delete/{id}', 'destroy');
});

Route::controller(ProductsController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/serach', 'search');
    Route::post('/products/post', 'store');
    Route::get('/products/list/{category_id}', 'show');
    Route::post('/products/update/{id}', 'update');
    Route::get('/products/delete/{id}', 'destroy');
});

Route::controller(CartsController::class)->group(function () {
    Route::get('/carts/list/{transaction_id}', 'index');
    Route::post('/carts/post', 'store');
    Route::get('/carts/pending', 'show');
    Route::post('/carts/update/{id}', 'update');
    Route::get('/carts/delete/{id}', 'destroy');
});

Route::controller(TransactionsController::class)->group(function () {
    Route::get('/transactions', 'index');
    Route::post('/transactions/post', 'store');
    Route::get('/transactions/details/{id}', 'show');
    Route::post('/transactions/update/{id}', 'update');
    Route::get('/transactions/delete/{id}', 'destroy');
});
