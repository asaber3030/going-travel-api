<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\TourImage;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Controller;

class TourImageController extends Controller
{
  use PaginateResources;

  protected $model = TourImage::class;

  public function index(Request $request)
  {
    $relationships = [
      'include_tour' => 'tour:id,duration,price,type',
      'include_created_by' => 'created_by:id,name',
      'include_updated_by' => 'updated_by:id,name',
      'include_deleted_by' => 'deleted_by:id,name',
    ];

    $queryModifier = function ($query, $request) {
      // Search by image_url
      if ($search = $request->query('search')) {
        $query->where('image_url', 'like', "%{$search}%");
      }

      // Filter by tour_id
      if ($tourId = $request->query('tour_id')) {
        $query->where('tour_id', $tourId);
      }

      // Sort by column
      if ($sortBy = $request->query('sort_by')) {
        $direction = $request->query('sort_direction', 'asc');
        $query->orderBy($sortBy, $direction);
      }
    };

    $data = $this->paginateResources($request, $relationships, 15, false, $queryModifier);

    return sendResponse(__('messages.retrieved_successfully'), 200, $data);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'tour_id' => 'required|exists:tours,id',
      'image_url' => 'required|image|max:2048',
    ]);

    if ($request->hasFile('image_url')) {
      $file = $request->file('image_url');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/tour_images'), $filename);
      $validated['image_url'] = 'uploads/tour_images/' . $filename;
    }

    $validated['created_by'] = Auth::id();
    $validated['updated_by'] = Auth::id();

    $image = TourImage::create($validated);

    return sendResponse(__('messages.created_successfully'), 201, $image->load('tour'));
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    $image = TourImage::with('tour')->find($id);

    if (!$image) {
      return sendResponse(__('messages.not_found'), 404);
    }

    return sendResponse(__('messages.retrieved_successfully'), 200, $image);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id)
  {
    $image = TourImage::find($id);

    if (!$image) {
      return sendResponse(__('messages.not_found'), 404);
    }

    $validated = $request->validate([
      'tour_id' => 'sometimes|exists:tours,id',
      'image_url' => 'sometimes|image|max:2048',
    ]);

    if ($request->hasFile('image_url')) {
      // Delete the old image if it exists
      if ($image->image_url && File::exists(public_path($image->image_url))) {
        File::delete(public_path($image->image_url));
      }
      $file = $request->file('image_url');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/tour_images'), $filename);
      $validated['image_url'] = 'uploads/tour_images/' . $filename;
    }

    $validated['updated_by'] = Auth::id();

    $image->update($validated);

    return sendResponse(__('messages.updated_successfully'), 200, $image->load('tour'));
  }

  /**
   * Remove the specified resource from storage (soft delete).
   */
  public function destroy($id)
  {
    $image = TourImage::find($id);

    if (!$image) {
      return sendResponse(__('messages.not_found'), 404);
    }

    $image->deleted_by = Auth::id();
    $image->save();
    $image->delete();

    return sendResponse(__('messages.deleted_successfully'), 200);
  }

  /**
   * Display a listing of trashed resources.
   */
  public function trashed(Request $request)
  {
    $relationships = [
      'include_tour' => 'tour:id,duration,price,type',
      'include_created_by' => 'created_by:id,name',
      'include_updated_by' => 'updated_by:id,name',
      'include_deleted_by' => 'deleted_by:id,name',
    ];

    $queryModifier = function ($query, $request) {
      // Search by image_url
      if ($search = $request->query('search')) {
        $query->where('image_url', 'like', "%{$search}%");
      }

      // Filter by tour_id
      if ($tourId = $request->query('tour_id')) {
        $query->where('tour_id', $tourId);
      }

      // Sort by column
      if ($sortBy = $request->query('sort_by')) {
        $direction = $request->query('sort_direction', 'asc');
        $query->orderBy($sortBy, $direction);
      }
    };

    $data = $this->paginateResources($request, $relationships, 15, true, $queryModifier);

    return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $data);
  }

  /**
   * Restore a trashed resource.
   */
  public function restore($id)
  {
    $image = TourImage::onlyTrashed()->find($id);

    if (!$image) {
      return sendResponse(__('messages.not_found_in_trash'), 404);
    }

    $image->deleted_by = null;
    $image->updated_by = Auth::id();
    $image->save();
    $image->restore();

    return sendResponse(__('messages.restored_successfully'), 200, $image->load('tour'));
  }
}
