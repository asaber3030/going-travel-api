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
use App\Http\Controllers\Dashboard\LimousineController;
use App\Http\Controllers\Dashboard\LimousineTranslationController;
use App\Http\Controllers\Dashboard\LimousineFeatureController;
use App\Http\Controllers\Dashboard\LimousineOverviewController;
use App\Http\Controllers\Dashboard\LimousineServiceController;
use App\Http\Controllers\Dashboard\LimousineSpecificationController;
use App\Http\Controllers\Dashboard\LimousineReviewController;

use App\Http\Controllers\UI\CategoryController as UICategoryController;
use App\Http\Controllers\UI\TourController as UITourController;
use App\Http\Controllers\UI\LocationController as UILocationController;
use App\Http\Controllers\UI\ReviewController as UIReviewController;

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
      Route::get('/all', 'all');
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
      Route::get('/all', 'all');
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
        Route::get('reviews', 'getReviews');
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

    Route::prefix('limousines')->middleware('auth')->group(function () {
      Route::get('/', [LimousineController::class, 'index'])->name('limousines.index');
      Route::get('{id}', [LimousineController::class, 'show'])->name('limousines.show');
      Route::post('/', [LimousineController::class, 'store'])->name('limousines.store');
      Route::put('{id}', [LimousineController::class, 'update'])->name('limousines.update');
      Route::delete('{id}', [LimousineController::class, 'destroy'])->name('limousines.destroy');
      Route::get('trashed', [LimousineController::class, 'trashed'])->name('limousines.trashed');
      Route::post('/restore/{id}', [LimousineController::class, 'restore'])->name('limousines.restore');
    });

    Route::prefix('limousine-translations')->middleware('auth')->group(function () {
      Route::get('/', [LimousineTranslationController::class, 'index'])->name('limousine-translations.index');
      Route::get('{id}', [LimousineTranslationController::class, 'show'])->name('limousine-translations.show');
      Route::post('/', [LimousineTranslationController::class, 'store'])->name('limousine-translations.store');
      Route::put('{id}', [LimousineTranslationController::class, 'update'])->name('limousine-translations.update');
      Route::delete('{id}', [LimousineTranslationController::class, 'destroy'])->name('limousine-translations.destroy');
      Route::get('trashed', [LimousineTranslationController::class, 'trashed'])->name('limousine-translations.trashed');
      Route::post('restore/{id}', [LimousineTranslationController::class, 'restore'])->name('limousine-translations.restore');
    });

    Route::prefix('limousine-features')->middleware('auth')->group(function () {
      Route::get('/', [LimousineFeatureController::class, 'index'])->name('limousine-features.index');
      Route::get('{id}', [LimousineFeatureController::class, 'show'])->name('limousine-features.show');
      Route::post('/', [LimousineFeatureController::class, 'store'])->name('limousine-features.store');
      Route::put('{id}', [LimousineFeatureController::class, 'update'])->name('limousine-features.update');
      Route::delete('{id}', [LimousineFeatureController::class, 'destroy'])->name('limousine-features.destroy');
      Route::get('trashed', [LimousineFeatureController::class, 'trashed'])->name('limousine-features.trashed');
      Route::post('restore/{id}', [LimousineFeatureController::class, 'restore'])->name('limousine-features.restore');
    });

    Route::prefix('limousine-overviews')->middleware('auth')->group(function () {
      Route::get('/', [LimousineOverviewController::class, 'index'])->name('limousine-overviews.index');
      Route::get('{id}', [LimousineOverviewController::class, 'show'])->name('limousine-overviews.show');
      Route::post('/', [LimousineOverviewController::class, 'store'])->name('limousine-overviews.store');
      Route::put('{id}', [LimousineOverviewController::class, 'update'])->name('limousine-overviews.update');
      Route::delete('{id}', [LimousineOverviewController::class, 'destroy'])->name('limousine-overviews.destroy');
      Route::get('trashed', [LimousineOverviewController::class, 'trashed'])->name('limousine-overviews.trashed');
      Route::put('restore/{id}', [LimousineOverviewController::class, 'restore'])->name('limousine-overviews.restore');
    });

    Route::prefix('limousine-services')->middleware('auth')->group(function () {
      Route::get('/', [LimousineServiceController::class, 'index'])->name('limousine-services.index');
      Route::get('{id}', [LimousineServiceController::class, 'show'])->name('limousine-services.show');
      Route::post('/', [LimousineServiceController::class, 'store'])->name('limousine-services.store');
      Route::put('{id}', [LimousineServiceController::class, 'update'])->name('limousine-services.update');
      Route::delete('{id}', [LimousineServiceController::class, 'destroy'])->name('limousine-services.destroy');
      Route::get('trashed', [LimousineServiceController::class, 'trashed'])->name('limousine-services.trashed');
      Route::put('restore/{id}', [LimousineServiceController::class, 'restore'])->name('limousine-services.restore');
    });


    Route::prefix('limousine-specifications')->middleware('auth')->group(function () {
      Route::get('/', [LimousineSpecificationController::class, 'index'])->name('limousine-specifications.index');
      Route::get('{id}', [LimousineSpecificationController::class, 'show'])->name('limousine-specifications.show');
      Route::post('/', [LimousineSpecificationController::class, 'store'])->name('limousine-specifications.store');
      Route::put('{id}', [LimousineSpecificationController::class, 'update'])->name('limousine-specifications.update');
      Route::delete('{id}', [LimousineSpecificationController::class, 'destroy'])->name('limousine-specifications.destroy');
      Route::get('trashed', [LimousineSpecificationController::class, 'trashed'])->name('limousine-specifications.trashed');
      Route::put('restore/{id}', [LimousineSpecificationController::class, 'restore'])->name('limousine-specifications.restore');
    });

    Route::prefix('limousine-reviews')->middleware('auth')->group(function () {
      Route::get('/', [LimousineReviewController::class, 'index'])->name('limousine-reviews.index');
      Route::get('{id}', [LimousineReviewController::class, 'show'])->name('limousine-reviews.show');
      Route::post('/', [LimousineReviewController::class, 'store'])->name('limousine-reviews.store');
      Route::put('{id}', [LimousineReviewController::class, 'update'])->name('limousine-reviews.update');
      Route::delete('{id}', [LimousineReviewController::class, 'destroy'])->name('limousine-reviews.destroy');
      Route::get('trashed', [LimousineReviewController::class, 'trashed'])->name('limousine-reviews.trashed');
      Route::put('restore/{id}', [LimousineReviewController::class, 'restore'])->name('limousine-reviews.restore');
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

Route::prefix('ui')->group(function () {
  Route::prefix('categories')->controller(UICategoryController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::get('/{id}/tours', 'category_tours');
  });

  Route::prefix('tours')->controller(UITourController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/all/paginated', 'paginated');
    Route::get('/all/popular-tours', 'popular_tours');
    Route::get('/{id}', 'show');
    Route::get('/{id}/related', 'related_tours');
  });

  Route::prefix('locations')->controller(UILocationController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::get('/{id}/tours', 'location_tours');
  });

  Route::prefix('reviews')->controller(UIReviewController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
  });
});
