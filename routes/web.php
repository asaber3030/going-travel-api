<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get("/", function () {
  return response()->json([
    'message' => 'Welcome to the API',
    'status' => 200
  ]);
});

Route::get('home', [HomeController::class, 'home'])->name('home');
Route::post('home', [HomeController::class, 'upload'])->name('home.upload');
