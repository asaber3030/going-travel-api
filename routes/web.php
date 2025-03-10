<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('home', [HomeController::class, 'home'])->name('home');
Route::post('home', [HomeController::class, 'upload'])->name('home.upload');
