<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\TourImage;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Controller;
use App\Models\LimousineImage;

class LimousineImageController extends Controller
{
  use PaginateResources;

  protected $model = LimousineImage::class;

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

      // Filter by limousine_id
      if ($tourId = $request->query('limousine_id')) {
        $query->where('limousine_id', $tourId);
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
      'limousine_id' => 'required|exists:limousines,id',
      'image' => 'required|image|max:2048',
    ]);

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/limousines_images'), $filename);
      $validated['image'] = 'uploads/limousines_images/' . $filename;
    }

    $validated['created_by'] = Auth::id();
    $validated['updated_by'] = Auth::id();

    $image = LimousineImage::create([
      'limousine_id' => $validated['limousine_id'],
      'url' => $validated['image'],
    ]);

    return sendResponse(__('messages.created_successfully'), 201, $image);
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    $image = LimousineImage::find($id);

    if (!$image) {
      return sendResponse(__('messages.not_found'), 404);
    }

    return sendResponse(__('messages.retrieved_successfully'), 200, $image);
  }


  public function update(Request $request, $id)
  {
    $image = LimousineImage::find($id);

    if (!$image) {
      return sendResponse(__('messages.not_found'), 404);
    }

    $validated = $request->validate([
      'url' => 'sometimes|image|max:2048',
    ]);

    if ($request->hasFile('image')) {
      if ($image->image && File::exists(public_path($image->image))) {
        File::delete(public_path($image->image));
      }
      $file = $request->file('image');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/limousines_images'), $filename);
      $validated['image'] = 'uploads/limousines_images/' . $filename;
    }

    $validated['updated_by'] = Auth::id();

    $image->update([
      'url' => $validated['image'],
    ]);

    return sendResponse(__('messages.updated_successfully'), 200, $image);
  }

  /**
   * Remove the specified resource from storage (soft delete).
   */
  public function destroy($id)
  {
    $image = LimousineImage::find($id);

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

      // Filter by limousine_id
      if ($tourId = $request->query('limousine_id')) {
        $query->where('limousine_id', $tourId);
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
    $image = LimousineImage::onlyTrashed()->find($id);

    if (!$image) {
      return sendResponse(__('messages.not_found_in_trash'), 404);
    }

    $image->deleted_by = null;
    $image->updated_by = Auth::id();
    $image->save();
    $image->restore();

    return sendResponse(__('messages.restored_successfully'), 200, $image);
  }
}
