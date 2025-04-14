<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Limousine;
use Illuminate\Http\Request;

class LimousineController extends Controller
{
	public function index(Request $request)
	{
		$query = Limousine::query();
		$orderBy = $request->query('order_by', 'id');
		$order = $request->query('order', 'desc');

		// Filter by type (multiple types)
		$types = $request->query('type');
		if ($types) {
			$typesArray = is_array($types) ? $types : explode(',', $types);
			$query->whereIn('type', $typesArray);
		}

		// Filter by max_passengers
		$maxPassengers = $request->query('max_passengers');
		if ($maxPassengers) {
			$query->where('max_passengers', '<=', $maxPassengers);
		}

		$limousines = $query
			->withCount('reviews')

			->with('reviews', 'features', 'specifications', 'overviews', 'services', 'translations')
			->orderBy($orderBy, $order)
			->paginate();

		return sendResponse(__('messages.retrieved_successfully'), 200, $limousines);
	}

	public function show($id)
	{
		$limousine = Limousine::with('reviews', 'translations', 'features', 'specifications', 'overviews', 'services')->find($id);

		if (!$limousine) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $limousine);
	}
}
