<?php

namespace App\Http\Controllers\UI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{

	public function index(Request $request)
	{
		$query = Review::query();
		$take = $request->query('take', 5);
		$orderBy = $request->query('order_by', 'rating');
		$order = $request->query('order', 'desc');

		$tours = $query
			->select('id', 'client_name', 'tour_id', 'rating', 'title', 'description', 'image')
			->orderBy($orderBy, $order)
			->with('tour', fn($query) => $query->select('id'))
			->take($take)
			->get();

		return sendResponse(__('messages.retrieved_successfully'), 200, $tours);
	}
}
