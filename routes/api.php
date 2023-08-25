<?php
namespace App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::resource('orders', OrderController::class)->except(['create', 'edit']);
Route::post('/orders/{id}/add', [OrderController::class, 'addProduct']);
Route::get('/orders/{id}/products', [OrderController::class, 'orderProducts']);
Route::post('/orders/{id}/pay', [OrderController::class, 'payOrder']);
