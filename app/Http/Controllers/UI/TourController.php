<?php

namespace App\Http\Controllers\UI;

use App\Models\Tour;
use App\Models\TourItinerary;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\TourExIn;
use App\Models\TourHighlight;
use App\Models\TourImage;

class TourController extends Controller
{
	use PaginateResources;

	protected $model = Tour::class;

	public function index(Request $request)
	{
		$query = Tour::query();
		$take = $request->query('take', 6);
		$orderBy = $request->query('order_by', 'id');
		$order = $request->query('order', 'desc');

		$tours = $query
			->select('id', 'duration', 'availability', 'type', 'banner', 'thumbnail', 'price_start', 'has_offer', 'offer_price')
			->orderBy($orderBy, $order)
			->withCount('reviews')
			->with('location')
			->take($take)
			->get();

		return sendResponse(__('messages.retrieved_successfully'), 200, $tours);
	}

	public function paginated(Request $request)
	{
		$query = Tour::query();
		$take = $request->query('take', 12);
		$search = $request->query('search');
		$orderBy = $request->query('order_by', 'id');
		$order = $request->query('order', 'desc');

		if ($search) {
			$query->whereHas('translations', function ($q) use ($search) {
				$q->where('title', 'like', '%' . $search . '%');
			});
		}

		$tours = $query
			->select('id', 'duration', 'availability', 'type', 'banner', 'thumbnail', 'price_start', 'has_offer', 'offer_price')
			->orderBy($orderBy, $order)
			->withCount('reviews')
			->with('location')
			->paginate($take);

		return sendResponse(__('messages.retrieved_successfully'), 200, $tours);
	}

	public function popular_tours(Request $request)
	{
		$query = Tour::query();
		$take = $request->query('take', 6);
		$orderBy = $request->query('order_by', 'id');
		$order = $request->query('order', 'desc');

		$tours = $query
			->select('id', 'duration', 'availability', 'type', 'banner', 'thumbnail', 'price_start', 'has_offer', 'offer_price')
			->withCount('reviews')
			->orderBy($orderBy, $order)
			->with('location')
			->take($take)
			->get();

		return sendResponse(__('messages.retrieved_successfully'), 200, $tours);
	}


	public function show($id)
	{
		$tour = Tour::with([
			'highlights' => fn($q) => $q->select('id', 'tour_id', 'image'),
			'reviews' => fn($q) => $q->select('id', 'client_name', 'rating', 'title', 'description', 'image', 'tour_id'),
			'itineraries' => fn($q) => $q->select('id', 'tour_id', 'day_number', 'image', 'meals', 'overnight_location'),
			'inclusions_exclusions' => fn($q) => $q->select('id', 'tour_id', 'type'),
			'images' => fn($q) => $q->select('id', 'tour_id', 'image_url'),
			'category' => fn($q) => $q->select('id', 'image'),
			'location' => fn($q) => $q->select('id', 'name', 'image', 'map_url'),
			'pickup_location' => fn($q) => $q->select('id', 'name', 'image', 'map_url'),
		])
			->withCount('reviews')
			->find($id);

		if (!$tour) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $tour);
	}

	public function related_tours($id)
	{
		$tour = Tour::find($id);
		$query = Tour::query();
		$tours = $query
			->select('id', 'duration', 'availability', 'type', 'banner', 'thumbnail', 'price_start', 'has_offer', 'offer_price')
			->withCount('reviews')
			->with('location')
			->where('id', '!=', $id)
			->orWhere('location_id', $tour->location_id)
			->orWhere('category_id', $tour->category_id)
			->take(10)
			->get();

		return sendResponse(__('messages.retrieved_successfully'), 200, $tours);
	}
}
