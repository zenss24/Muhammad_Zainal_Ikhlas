<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OAuthController;



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

Route::middleware('auth.token')->get('/me', function (Request $request) {
    return $request->user();
});

// Auth routes

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth.token');
Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');


// Products routes
Route::middleware('auth.token')->resource('products', ProductController::class);

// Categories routes
Route::middleware(['auth.token', 'admin'])->resource('categories', CategoryController::class);

Route::get('/login/google', [OAuthController::class, 'redirectToGoogle'])->name('oauth.google');
Route::get('/login/google/callback', [OAuthController::class, 'handleGoogleCallback']);