<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AliexpressController;

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

//Route::get('/ali/{url}/{type?}', [AliexpressController::class, 'index']);
Route::get('/ali/{id}', [AliexpressController::class, 'index']);
//Route::get('/ali', [AliexpressController::class, 'teste']);
//Route::get('/product/{id}', [AliexpressController::class, 'getProductInfo']);
//Route::get('/product/{url}', [AliexpressController::class, 'getProductInfo']);