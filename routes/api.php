<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\Dashboard\CategoryTranslationController;
use App\Http\Controllers\Dashboard\ReviewController;
use App\Http\Controllers\Dashboard\TourController;
use App\Http\Controllers\Dashboard\TourExInTranslationController;
use App\Http\Controllers\Dashboard\TourTranslationController;
use App\Http\Controllers\Dashboard\TourHighlightController;
use App\Http\Controllers\Dashboard\TourHighlightTranslationController;
use App\Http\Controllers\Dashboard\TourImageController;
use App\Http\Controllers\Dashboard\TourItineraryController;
use App\Http\Controllers\Dashboard\TourItineraryTranslationController;
use App\Http\Controllers\Dashboard\TourExInController;

use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
  Route::post('login', 'login')->name('login');
  Route::post('register', 'register')->name('register');

  Route::get('me', 'getCurrentUser')->name('me')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
  Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('locations')->controller(LocationController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('category-translations')->controller(CategoryTranslationController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('tours')->controller(TourController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');

      Route::prefix('{id}')->group(function () {
        Route::get('/', 'show');
        Route::post('/', 'update');
        Route::delete('/', 'destroy');
        Route::post('/restore', 'restore');

        Route::get('translations', 'getTranslations');
        Route::get('highlights', 'getHighlights');
        Route::get('itineraries', 'getItineraries');
        Route::get('inclusions-exclusions', 'getInclusionsExclusions');
        Route::get('images', 'getImages');
      });
    });

    Route::prefix('tour-images')->controller(TourImageController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('tour-translations')->controller(TourTranslationController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('tour-highlights')->controller(TourHighlightController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('tour-highlights-translations')->controller(TourHighlightTranslationController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });


    Route::prefix('tour-itineraries')->controller(TourItineraryController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('tour-itineraries-translations')->controller(TourItineraryTranslationController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('tour-inclusions-exclusions')->controller(TourExInController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('tour-inclusions-exclusions-translations')->controller(TourExInTranslationController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });
  });
});
