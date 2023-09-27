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

// Route::middleware('auth:api')->group(function () {
    Route::post('/petshop/profile-edit/post', [AuthController::class, 'update']);
    Route::post('/petshop/logout', [AuthController::class, 'logout']);

    Route::controller(CategoriesController::class)->prefix('categories')->group(function () {
        Route::get('', 'indexMaster')->name('categories');
        Route::get('/create-category', 'createMaster')->name('');
        Route::post('/post', 'storeMaster')->name('');
        Route::get('/edit-category/{id}', 'editMaster')->name('');
        Route::patch('/update/{id}', 'updateMaster')->name('');
        Route::get('/delete/{id}', 'destroyMaster')->name('');
    });

    Route::controller(UnitsController::class)->prefix('units')->group(function () {
        Route::get('', 'indexMaster')->name('units');
        Route::get('/create-unit', 'createMaster')->name('');
        Route::post('/post', 'storeMaster')->name('');
        Route::get('/edit-category/{id}', 'editMaster')->name('');
        Route::patch('/update/{id}', 'updateMaster')->name('');
        Route::get('/delete/{id}', 'destroyMaster')->name('');
    });

    Route::controller(ProductsController::class)->group(function () {
        Route::get('/products', 'indexMaster')->name('products');
        Route::get('/product/details/{id}', 'show')->name('');
        Route::get('/create-product', 'createMaster')->name('');
        Route::post('/product/post', 'store')->name('');
        Route::get('/edit-product/{id}', 'editMaster')->name('');
        Route::post('/product/update/{id}', 'update')->name('');
        Route::get('/product/delete/{id}', 'destroy')->name('');
    });

    Route::controller(TransactionsController::class)->prefix('transactions')->group(function () {
        Route::get('', 'indexMaster')->name('transactions');
        Route::get('/transaction-details/{transaction_id}', 'detailsMaster')->name('');
        Route::get('/transactions-delete/{id}', 'destroyMaster')->name('');
    });
// });
