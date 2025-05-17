<?php

namespace App\Http\Controllers\UI;

use App\Models\Hotel;
use App\Models\HotelTranslation;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HotelController extends Controller
{
	use PaginateResources;

	protected $model = Hotel::class;

	// Get list of hotels with basic data
	public function index(Request $request)
	{
		try {
			$query = Hotel::query();
			$take = $request->query('take', 6);
			$orderBy = $request->query('order_by', 'id');
			$order = $request->query('order', 'desc');
			$location_id = $request->query('location_id');

			if ($location_id) {
				$query->where('location_id', $location_id);
			}

			$hotels = $query
				->orderBy($orderBy, $order)
				->with('translations', 'amenity', 'location', 'category')
				->take($take)
				->get();

			return sendResponse(__('messages.retrieved_successfully'), 200, $hotels);
		} catch (\Exception $e) {
			return response()->json([
				'message' => $e->getMessage(),
				'status' => 500
			], 500);
		}
	}

	// Get paginated list of hotels with optional filters
	public function paginated(Request $request)
	{
		try {
			$query = Hotel::query();
			$take = $request->query('take', 12);
			$search = $request->query('search');
			$orderBy = $request->query('order_by', 'id');
			$order = $request->query('order', 'desc');
			$location_id = $request->query('location_id');

			if ($search) {
				$query->where('name', 'like', '%' . $search . '%'); // Searching by hotel name
			}

			if ($location_id) {
				$query->where('location_id', $location_id);
			}

			$hotels = $query
				->orderBy($orderBy, $order)
				->with('location', 'amenity', 'category', 'translations')
				->paginate($take);


			return sendResponse(__('messages.retrieved_successfully'), 200, $hotels);
		} catch (\Exception $e) {
			return response()->json([
				'message' => $e->getMessage(),
				'status' => 500
			], 500);
		}
	}


	public function show($hotel_id)
	{
		$hotel = Hotel::with('translations', 'amenity', 'reviews', 'location', 'category')->find($hotel_id);
		return sendResponse(__('messages.retrieved_successfully'), 200, $hotel);
	}
}
