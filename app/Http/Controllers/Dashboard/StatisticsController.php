<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Limousine;
use App\Models\Hotel;
use App\Models\Location; // Assuming your Location model is named 'Location'
use App\Models\Category; // Assuming your Category model is named 'Category'
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function index(): JsonResponse
    {
        $totalTours = Tour::count();
        $totalLimousines = Limousine::count();
        $totalHotels = Hotel::count();
        $totalLocations = Location::count();
        $totalCategories = Category::count();

        $statistics = [
            'total_tours' => $totalTours,
            'total_limousines' => $totalLimousines,
            'total_hotels' => $totalHotels,
            'total_locations' => $totalLocations,
            'total_categories' => $totalCategories,
        ];

        return response()->json([
            'message' => __('messages.retrieved_successfully'),
            'status_code' => 200,
            'data' => $statistics,
        ]);
    }
}