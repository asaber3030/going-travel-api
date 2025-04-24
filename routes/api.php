<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\Dashboard\CategoryTranslationController;
use App\Http\Controllers\Dashboard\ReviewController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\ServiceCardController;
use App\Http\Controllers\Dashboard\ServiceCardTranslationController;
use App\Http\Controllers\Dashboard\TourController;
use App\Http\Controllers\Dashboard\HajController;
use App\Http\Controllers\Dashboard\HajDayController;
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
use App\Http\Controllers\Dashboard\LimousineImageController;
use App\Http\Controllers\Dashboard\LimousineOverviewController;
use App\Http\Controllers\Dashboard\LimousineServiceController;
use App\Http\Controllers\Dashboard\LimousineSpecificationController;
use App\Http\Controllers\Dashboard\LimousineReviewController;
use App\Http\Controllers\Dashboard\HotelController;
use App\Http\Controllers\Dashboard\HotelTranslationController;
use App\Http\Controllers\Dashboard\StatisticsController;

use App\Http\Controllers\UI\HotelController as UIHotelController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\UI\CategoryController as UICategoryController;
use App\Http\Controllers\UI\TourController as UITourController;
use App\Http\Controllers\UI\LocationController as UILocationController;
use App\Http\Controllers\UI\ReviewController as UIReviewController;
use App\Http\Controllers\UI\LimousineController as UILimousineController;
use App\Http\Controllers\UI\HajController as UIHajController;
use App\Http\Controllers\UI\ServiceCardController as UIServiceCardController;
use App\Http\Controllers\UI\SettingsController as UISettingsController;

use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
  Route::post('login', 'login')->name('login');
  Route::post('register', 'register')->name('register');

  Route::get('me', 'getCurrentUser')->name('me')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
  Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/statistics', [StatisticsController::class, 'index'])->name('dashboard.statistics');

    Route::prefix('settings')->controller(SettingsController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('by-key/{key}', 'show');
      Route::get('by-group/{group}', 'get_by_group');
      Route::post('update/{key}', 'update');
      Route::post('create', 'store');
    });

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

    Route::prefix('service-cards')->controller(ServiceCardController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/all', 'all');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });

    Route::prefix('service-card-translations')->controller(ServiceCardTranslationController::class)->group(function () {
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

    Route::prefix('users')->group(function () {
      Route::get('/', [UserController::class, 'index'])->name('users.index');
      Route::get('{id}', [UserController::class, 'show'])->name('users.show');
      Route::post('/', [UserController::class, 'store'])->name('users.store');
      Route::patch('{id}', [UserController::class, 'update'])->name('users.update');
      Route::delete('{id}', [UserController::class, 'destroy'])->name('users.destroy');
      Route::get('trashed', [UserController::class, 'trashed'])->name('users.trashed');
      Route::get('all', [UserController::class, 'allUsers'])->name('users.all'); //testing purpose
    });

    Route::prefix('hajs')->group(function () {
      Route::get('/', [HajController::class, 'index'])->name('hajs.index');
      Route::get('{id}', [HajController::class, 'show'])->name('hajs.show');
      Route::post('/', [HajController::class, 'store'])->name('hajs.store');
      Route::post('{id}', [HajController::class, 'update'])->name('hajs.update');
      Route::delete('{id}', [HajController::class, 'destroy'])->name('hajs.destroy');
      Route::get('trashed', [HajController::class, 'trashed'])->name('hajs.trashed');
    });

    Route::prefix('haj-days')->group(function () {
      Route::get('/', [HajDayController::class, 'index'])->name('haj-days.index');
      Route::get('{id}', [HajDayController::class, 'show'])->name('haj-days.show');
      Route::post('/', [HajDayController::class, 'store'])->name('haj-days.store');
      Route::post('{id}', [HajDayController::class, 'update'])->name('haj-days.update');
      Route::delete('{id}', [HajDayController::class, 'destroy'])->name('haj-days.destroy');
      Route::get('trashed', [HajDayController::class, 'trashed'])->name('haj-days.trashed');
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

    Route::prefix('limousines')->group(function () {
      Route::get('/', [LimousineController::class, 'index'])->name('limousines.index');
      Route::get('{id}', [LimousineController::class, 'show'])->name('limousines.show');
      Route::get('{id}/reviews', [LimousineController::class, 'reviews'])->name('limousines.reviews');
      Route::get('{id}/services', [LimousineController::class, 'services'])->name('limousines.services');
      Route::get('{id}/overviews', [LimousineController::class, 'overviews'])->name('limousines.overviews');
      Route::get('{id}/features', [LimousineController::class, 'features'])->name('limousines.features');
      Route::get('{id}/specifications', [LimousineController::class, 'specifications'])->name('limousines.specifications');
      Route::get('{id}/images', [LimousineController::class, 'images'])->name('limousines.images');
      Route::get('{id}/', [LimousineController::class, 'show'])->name('limousines.show');
      Route::post('/', [LimousineController::class, 'store'])->name('limousines.store');
      Route::post('{id}', [LimousineController::class, 'update'])->name('limousines.update');
      Route::delete('{id}', [LimousineController::class, 'destroy'])->name('limousines.destroy');
      Route::get('trashed', [LimousineController::class, 'trashed'])->name('limousines.trashed');
      Route::post('/{id}/restore', [LimousineController::class, 'restore'])->name('limousines.restore');
    });

    Route::prefix('limousine-images')->controller(LimousineImageController::class)->group(function () {
      Route::get('/', 'index');
      Route::get('/trashed', 'trashed');
      Route::post('/', 'store');
      Route::get('/{id}', 'show');
      Route::post('/{id}', 'update');
      Route::delete('/{id}', 'destroy');
      Route::post('/{id}/restore', 'restore');
    });


    Route::prefix('limousine-translations')->group(function () {
      Route::get('/', [LimousineTranslationController::class, 'index'])->name('limousine-translations.index');
      Route::get('{id}', [LimousineTranslationController::class, 'show'])->name('limousine-translations.show');
      Route::post('/', [LimousineTranslationController::class, 'store'])->name('limousine-translations.store');
      Route::put('{id}', [LimousineTranslationController::class, 'update'])->name('limousine-translations.update');
      Route::delete('{id}', [LimousineTranslationController::class, 'destroy'])->name('limousine-translations.destroy');
      Route::get('trashed', [LimousineTranslationController::class, 'trashed'])->name('limousine-translations.trashed');
      Route::post('{id}/restore', [LimousineTranslationController::class, 'restore'])->name('limousine-translations.restore');
    });

    Route::prefix('limousine-features')->group(function () {
      Route::get('/', [LimousineFeatureController::class, 'index'])->name('limousine-features.index');
      Route::get('{id}', [LimousineFeatureController::class, 'show'])->name('limousine-features.show');
      Route::post('/', [LimousineFeatureController::class, 'store'])->name('limousine-features.store');
      Route::put('{id}', [LimousineFeatureController::class, 'update'])->name('limousine-features.update');
      Route::delete('{id}', [LimousineFeatureController::class, 'destroy'])->name('limousine-features.destroy');
      Route::get('trashed', [LimousineFeatureController::class, 'trashed'])->name('limousine-features.trashed');
      Route::post('{id}/restore', [LimousineFeatureController::class, 'restore'])->name('limousine-features.restore');
    });

    Route::prefix('limousine-overviews')->group(function () {
      Route::get('/', [LimousineOverviewController::class, 'index'])->name('limousine-overviews.index');
      Route::get('{id}', [LimousineOverviewController::class, 'show'])->name('limousine-overviews.show');
      Route::post('/', [LimousineOverviewController::class, 'store'])->name('limousine-overviews.store');
      Route::put('{id}', [LimousineOverviewController::class, 'update'])->name('limousine-overviews.update');
      Route::delete('{id}', [LimousineOverviewController::class, 'destroy'])->name('limousine-overviews.destroy');
      Route::get('trashed', [LimousineOverviewController::class, 'trashed'])->name('limousine-overviews.trashed');
      Route::post('{id}/restore', [LimousineOverviewController::class, 'restore'])->name('limousine-overviews.restore');
    });

    Route::prefix('limousine-services')->group(function () {
      Route::get('/', [LimousineServiceController::class, 'index'])->name('limousine-services.index');
      Route::get('{id}', [LimousineServiceController::class, 'show'])->name('limousine-services.show');
      Route::post('/', [LimousineServiceController::class, 'store'])->name('limousine-services.store');
      Route::put('{id}', [LimousineServiceController::class, 'update'])->name('limousine-services.update');
      Route::delete('{id}', [LimousineServiceController::class, 'destroy'])->name('limousine-services.destroy');
      Route::get('trashed', [LimousineServiceController::class, 'trashed'])->name('limousine-services.trashed');
      Route::post('{id}/restore', [LimousineServiceController::class, 'restore'])->name('limousine-services.restore');
    });


    Route::prefix('limousine-specifications')->group(function () {
      Route::get('/', [LimousineSpecificationController::class, 'index'])->name('limousine-specifications.index');
      Route::get('{id}', [LimousineSpecificationController::class, 'show'])->name('limousine-specifications.show');
      Route::post('/', [LimousineSpecificationController::class, 'store'])->name('limousine-specifications.store');
      Route::put('{id}', [LimousineSpecificationController::class, 'update'])->name('limousine-specifications.update');
      Route::delete('{id}', [LimousineSpecificationController::class, 'destroy'])->name('limousine-specifications.destroy');
      Route::get('trashed', [LimousineSpecificationController::class, 'trashed'])->name('limousine-specifications.trashed');
      Route::post('{id}/restore', [LimousineSpecificationController::class, 'restore'])->name('limousine-specifications.restore');
    });

    Route::prefix('limousine-reviews')->group(function () {
      Route::get('/', [LimousineReviewController::class, 'index'])->name('limousine-reviews.index');
      Route::get('{id}', [LimousineReviewController::class, 'show'])->name('limousine-reviews.show');
      Route::post('/', [LimousineReviewController::class, 'store'])->name('limousine-reviews.store');
      Route::put('{id}', [LimousineReviewController::class, 'update'])->name('limousine-reviews.update');
      Route::delete('{id}', [LimousineReviewController::class, 'destroy'])->name('limousine-reviews.destroy');
      Route::get('trashed', [LimousineReviewController::class, 'trashed'])->name('limousine-reviews.trashed');
      Route::post('{id}/restore', [LimousineReviewController::class, 'restore'])->name('limousine-reviews.restore');
    });

    Route::prefix('hotels')->group(function () {
      Route::get('/', [HotelController::class, 'index'])->name('hotels.index');
      Route::get('{id}', [HotelController::class, 'show'])->name('hotels.show');
      Route::post('/', [HotelController::class, 'store'])->name('hotels.store');
      Route::post('{id}', [HotelController::class, 'update'])->name('hotels.update');
      Route::delete('{id}', [HotelController::class, 'destroy'])->name('hotels.destroy');
      Route::get('trashed', [HotelController::class, 'trashed'])->name('hotels.trashed');
      Route::post('restore/{id}', [HotelController::class, 'restore'])->name('hotels.restore');
    });

    Route::prefix('hotel-translations')->group(function () {
      Route::get('/', [HotelTranslationController::class, 'index'])->name('hotel-translations.index');
      Route::get('{id}', [HotelTranslationController::class, 'show'])->name('hotel-translations.show');
      Route::post('/', [HotelTranslationController::class, 'store'])->name('hotel-translations.store');
      Route::put('{id}', [HotelTranslationController::class, 'update'])->name('hotel-translations.update');
      Route::delete('{id}', [HotelTranslationController::class, 'destroy'])->name('hotel-translations.destroy');
      Route::get('trashed', [HotelTranslationController::class, 'trashed'])->name('hotel-translations.trashed');
      Route::post('restore/{id}', [HotelTranslationController::class, 'restore'])->name('hotel-translations.restore');
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

  Route::prefix('limousines')->controller(UILimousineController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
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

  Route::prefix('hajs')->controller(UIHajController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
  });

  Route::prefix('service-cards')->controller(UIServiceCardController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
  });

  Route::prefix('settings')->controller(UISettingsController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('by-key/{key}', 'show');
    Route::get('by-group/{group}', 'get_by_group');
  });

  Route::prefix('hotels')->controller(HotelController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/all/paginated', 'paginated');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
  });
});
