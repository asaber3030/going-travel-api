<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\PaginateResources;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;


class CategoryController extends Controller
{
  use PaginateResources;

  protected $model = Category::class;

  public function index(Request $request)
  {
    $relationships = [
      'include_created_by' => 'created_by:id,name',
      'include_updated_by' => 'updated_by:id,name',
      'include_deleted_by' => 'deleted_by:id,name',
      'include_translations' => 'translations:category_id,locale,name,description',
    ];

    $queryModifier = function ($query, $request) {
      if ($search = $request->query('search')) {
        $query->whereHas('translations', function ($q) use ($search) {
          $q->where('name', 'like', "%{$search}%");
        });
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

  public function store(Request $request)
  {
    $validated = $request->validate([
      'image' => 'nullable|image|max:2048', // Max 2MB, must be image
      'translations' => 'sometimes|array', // Optional translations
      'translations.*.language' => 'required_with:translations|string',
      'translations.*.name' => 'required_with:translations|string|max:255',
      'translations.*.description' => 'nullable|string',
    ]);

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/categories'), $filename);
      $validated['image'] = 'uploads/categories/' . $filename;
    }

    $validated['created_by'] = Auth::id();
    $validated['updated_by'] = Auth::id();

    $category = Category::create($validated);

    // Handle translations if provided
    if ($request->has('translations')) {
      $category->translations()->createMany($request->input('translations'));
    }

    return sendResponse(__('messages.created_successfully'), 201, $category->load('translations'));
  }

  public function show($id)
  {
    $category = Category::with('translations')->find($id);

    if (!$category) {
      return sendResponse(__('messages.not_found'), 404);
    }

    return sendResponse(__('messages.retrieved_successfully'), 200, $category);
  }

  public function update(Request $request, $id)
  {
    $category = Category::find($id);

    if (!$category) {
      return sendResponse(__('messages.not_found'), 404);
    }

    $validated = $request->validate([
      'image' => 'sometimes|nullable|image|max:2048',
      'translations' => 'sometimes|array',
      'translations.*.language' => 'required_with:translations|string',
      'translations.*.name' => 'required_with:translations|string|max:255',
      'translations.*.description' => 'nullable|string',
    ]);

    if ($request->hasFile('image')) {
      if ($category->image && File::exists(public_path($category->image))) {
        File::delete(public_path($category->image));
      }
      $file = $request->file('image');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->move(public_path('uploads/categories'), $filename);
      $validated['image'] = 'uploads/categories/' . $filename;
    }

    $validated['updated_by'] = Auth::id();

    $category->update($validated);

    // Handle translations if provided
    if ($request->has('translations')) {
      $category->translations()->delete(); // Remove old translations
      $category->translations()->createMany($request->input('translations'));
    }

    return sendResponse(__('messages.updated_successfully'), 200, $category->load('translations'));
  }

  public function destroy($id)
  {
    $category = Category::find($id);

    if (!$category) {
      return sendResponse(__('messages.not_found'), 404);
    }

    $category->deleted_by = Auth::id();
    $category->save();
    $category->delete();

    return sendResponse(__('messages.deleted_successfully'), 200);
  }

  public function trashed(Request $request)
  {
    $relationships = [
      'include_created_by' => 'created_by:id,name',
      'include_updated_by' => 'updated_by:id,name',
      'include_deleted_by' => 'deleted_by:id,name',
      'include_translations' => 'translations:category_id,language,name,description',
    ];

    $queryModifier = function ($query, $request) {
      if ($search = $request->query('search')) {
        $query->whereHas('translations', function ($q) use ($search) {
          $q->where('name', 'like', "%{$search}%");
        });
      }

      if ($deletedBy = $request->query('deleted_by')) {
        $query->where('deleted_by', $deletedBy);
      }

      if ($sortBy = $request->query('sort_by')) {
        $direction = $request->query('sort_direction', 'asc');
        $query->orderBy($sortBy, $direction);
      }
    };

    $data = $this->paginateResources($request, $relationships, 15, true, $queryModifier);

    return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $data);
  }

  public function restore($id)
  {
    $category = Category::onlyTrashed()->find($id);

    if (!$category) {
      return sendResponse(__('messages.not_found_in_trash'), 404);
    }

    $category->deleted_by = null;
    $category->updated_by = Auth::id();
    $category->save();
    $category->restore();

    return sendResponse(__('messages.restored_successfully'), 200, $category->load('translations'));
  }
}
