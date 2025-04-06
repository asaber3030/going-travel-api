<?php

namespace App\Http\Controllers\UI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tour;
use App\Traits\PaginateResources;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

  public function index(Request $request)
  {
    $query = Category::query();
    $take = $request->query('take', 6);
    $orderBy = $request->query('order_by', 'id');
    $order = $request->query('order', 'desc');

    $categories = $query
      ->select('id', 'image')
      ->orderBy($orderBy, $order)
      ->take($take)
      ->get();

    return sendResponse(__('messages.retrieved_successfully'), 200, $categories);
  }

  public function category_tours(Request $request, $id)
  {
    $query = Tour::query();
    $orderBy = $request->query('order_by', 'id');
    $order = $request->query('order', 'desc');

    $query->where('category_id', $id);
    $query
      ->with('location')
      ->withCount('reviews');

    $tours = $query->orderBy($orderBy, $order)->paginate();

    return sendResponse(__('messages.retrieved_successfully'), 200, $tours);
  }


  public function show($id)
  {
    $category = Category::find($id);

    if (!$category) {
      return sendResponse(__('messages.not_found'), 404);
    }

    return sendResponse(__('messages.retrieved_successfully'), 200, $category);
  }
}
