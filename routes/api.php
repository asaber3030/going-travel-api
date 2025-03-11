<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
  Route::post('login', 'login')->name('login');
  Route::post('register', 'register')->name('register');

  Route::get('me', 'getCurrentUser')->name('me')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
  Route::prefix('admin')->name('admin.')->group(function () {});
});
