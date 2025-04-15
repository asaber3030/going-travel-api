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

		$limousines = $query
			->with('reviews', 'features', 'specifications', 'overviews', 'services', 'translations')
			->withCount('reviews')
			->orderBy($orderBy, $order)
			->paginate();

		return sendResponse(__('messages.retrieved_successfully'), 200, $limousines);
	}

	public function show($id)
	{
		$limousine = Limousine::with('reviews', 'translations', 'images', 'features', 'specifications', 'overviews', 'services')
			->withCount('reviews')
			->find($id);
		if (!$limousine) return sendResponse(__('messages.not_found'), 404);
		return sendResponse(__('messages.retrieved_successfully'), 200, $limousine);
	}
}
