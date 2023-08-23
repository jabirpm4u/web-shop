<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('api')->group(function () {

    Route::post('import-master-data', 'ImportController@import');
    
    Route::resource('orders', 'OrderController')->except(['create', 'edit']);
    Route::post('orders/{id}/add', 'OrderController@addProduct');
    Route::post('orders/{id}/pay', 'OrderController@payOrder');
    
    // Route::resource('customers', 'CustomerController')->except(['create', 'edit', 'index', 'store', 'show', 'update', 'destroy']);
    // Route::resource('products', 'ProductController')->except(['create', 'edit', 'index', 'store', 'show', 'update', 'destroy']);
});
