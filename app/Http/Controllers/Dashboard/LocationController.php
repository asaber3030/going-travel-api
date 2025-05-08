<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Traits\PaginateResources;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LocationController extends Controller
{

  use PaginateResources;

  public $model = Location::class;

  public function index(Request $request)
  {
    $relationships = [
      'include_created_by' => 'created_by:id,name',
      'include_updated_by' => 'updated_by:id,name',
      'include_deleted_by' => 'deleted_by:id,name',
    ];

    $queryModifier = function ($query, $request) {
      if ($search = $request->query('search')) {
        $query->where('name', 'like', "%{$search}%");
      }

      if ($createdBy = $request->query('created_by')) {
        $query->where('created_by', $createdBy);
      }

      if ($sortBy = $request->query('sort_by')) {
        $direction = $request->query('sort_direction', 'asc');
        $query->orderBy($sortBy, $direction);
      }
    };

    $data = $this->paginateResources($request, $relationships, 15, false, $queryModifier);

    return sendResponse(__('messages.retrieved_successfully'), 200, $data);
  }

  public function all(Request $request)
  {
    $search = $request->query('search');
    $per_page = $request->query('per_page') ?? 20;
    $locations = Location::query();

    if ($search) {
      $locations->where('name', 'like', "%{$search}%");
    }

    $data = $locations->take($per_page)->get();
    return sendResponse(__('messages.retrieved_successfully'), 200, $data);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'image' => 'nullable|image|max:1024',

    ]);

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/locations'), $filename);
      $validated['image'] = 'uploads/locations/' . $filename;
    }

    $validated['created_by'] = Auth::id();
    $validated['updated_by'] = Auth::id();

    $location = Location::create($validated);

    return sendResponse(__('messages.created_successfully'), 201, $location);
  }

  public function show($id)
  {
    $location = Location::find($id);

    if (!$location) {
      return sendResponse(__('messages.not_found'), 404);
    }

    return sendResponse(__('messages.retrieved_successfully'), 200, $location);
  }

  public function update(Request $request, $id)
  {
    $location = Location::find($id);

    if (!$location) {
      return sendResponse(__('messages.not_found'), 404);
    }

    $validated = $request->validate([
      'name' => 'sometimes|string|max:255',
      'image' => 'sometimes|nullable|image|max:1024',
    ]);

    if ($request->hasFile('image')) {
      if ($location->image && File::exists(public_path($location->image))) {
        File::delete(public_path($location->image));
      }
      $file = $request->file('image');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/locations'), $filename);
      $validated['image'] = 'uploads/locations/' . $filename;
    } else {
      $validated['image'] = $location->image;
    }

    $validated['updated_by'] = Auth::id();
    $location->update($validated);
    return sendResponse(__('messages.updated_successfully'), 200, $location);
  }

  public function destroy($id)
  {
    $location = Location::find($id);

    if (!$location) {
      return sendResponse(__('messages.not_found'), 404);
    }

    $location->deleted_by = Auth::id();
    $location->save();
    $location->delete();

    return sendResponse(__('messages.deleted_successfully'), 200);
  }

  public function trashed(Request $request)
  {
    $perPage = $request->query('per_page', 15);
    $locations = Location::onlyTrashed()->paginate($perPage);
    return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $locations);
  }

  public function restore($id)
  {
    $location = Location::onlyTrashed()->find($id);

    if (!$location) {
      return sendResponse(__('messages.not_found_in_trash'), 404);
    }

    $location->deleted_by = null;
    $location->updated_by = Auth::id();
    $location->save();
    $location->restore();

    return sendResponse(__('messages.restored_successfully'), 200, $location);
  }
}
