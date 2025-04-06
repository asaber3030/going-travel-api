<?php

namespace App\Http\Controllers\UI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Tour;
use App\Traits\PaginateResources;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{

  public function index(Request $request)
  {
    $query = Location::query();
    $take = $request->query('take', 6);
    $orderBy = $request->query('order_by', 'name');
    $order = $request->query('order', 'asc');

    $locations = $query
      ->select('id', 'name', 'image', 'map_url')
      ->orderBy($orderBy, $order)
      ->take($take)
      ->get();

    return sendResponse(__('messages.retrieved_successfully'), 200, $locations);
  }

  public function location_tours(Request $request, $id)
  {
    $query = Tour::query();
    $orderBy = $request->query('order_by', 'id');
    $order = $request->query('order', 'desc');

    $query->where('location_id', $id);
    $query
      ->with('location')
      ->withCount('reviews');

    $tours = $query->orderBy($orderBy, $order)->paginate();

    return sendResponse(__('messages.retrieved_successfully'), 200, $tours);
  }


  public function show($id)
  {
    $location = Location::find($id);

    if (!$location) {
      return sendResponse(__('messages.not_found'), 404);
    }

    return sendResponse(__('messages.retrieved_successfully'), 200, $location);
  }
}
