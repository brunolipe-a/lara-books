<?php

use App\Http\Controllers\V1\BookController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('users.books', BookController::class);
    });
