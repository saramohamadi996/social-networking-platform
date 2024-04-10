<?php

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login')->middleware('throttle:3,1');

    Route::apiResource('posts', 'App\Http\Controllers\PostController');
    Route::apiResource('comments', 'App\Http\Controllers\CommentController')->only(['index', 'store', 'show', 'destroy']);
    Route::post('comments/{comment}/reply', 'App\Http\Controllers\CommentController@storeReply');
});

