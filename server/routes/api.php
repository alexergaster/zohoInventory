<?php


use App\Http\Controllers\ContactsController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\SaleOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/items', [ItemsController::class, "index"]);
Route::post('/items', [ItemsController::class, "store"]);
Route::group(['prefix' => 'contacts'], function () {
    Route::get('/', [ContactsController::class, "index"]);
    Route::post('/', [ContactsController::class, "store"]);
});
Route::post('/salesorders', [SaleOrderController::class, "store"]);
