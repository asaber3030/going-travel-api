<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Limousine;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LimousineController extends Controller
{
    use PaginateResources;

    protected $model = Limousine::class;

    public function index(Request $request)
    {
        $relationships = [
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
            'include_translations' => 'translations:limousine_id,locale,name,description',
            'include_category' => 'category:id,name',
            'include_location' => 'location:id,name',
        ];

        $queryModifier = function ($query, $request) {
            $query->orderBy('id', 'desc');

            if ($search = $request->query('search')) {
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($categoryId = $request->query('category_id')) {
                $query->where('category_id', $categoryId);
            }

            if ($locationId = $request->query('location_id')) {
                $query->where('location_id', $locationId);
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
            'type' => 'required|string',
            'price_per_hour' => 'required|numeric|min:0',
            'max_passengers' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'image' => 'nullable|image|max:2048',
            'translations' => 'sometimes|array',
            'translations.*.locale' => 'required_with:translations|string',
            'translations.*.name' => 'required_with:translations|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_limousine_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/limousines'), $filename);
            $validated['image'] = 'uploads/limousines/' . $filename;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $limousine = Limousine::create($validated);

        if ($request->has('translations')) {
            $translations = array_map(function ($t) {
                return [
                    ...$t,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];
            }, $request->translations);

            $limousine->translations()->createMany($translations);
        }

        return sendResponse(__('messages.created_successfully'), 201, $limousine->load('translations', 'category', 'location'));
    }

    public function show($id)
    {
        $limousine = Limousine::with('translations', 'category', 'location')->find($id);

        if (!$limousine) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $limousine);
    }

    public function update(Request $request, $id)
    {
        $limousine = Limousine::find($id);

        if (!$limousine) return sendResponse(__('messages.not_found'), 404);

        $validated = $request->validate([
            'type' => 'sometimes|string',
            'price_per_hour' => 'sometimes|numeric|min:0',
            'max_passengers' => 'sometimes|integer|min:1',
            'category_id' => 'sometimes|exists:categories,id',
            'location_id' => 'sometimes|exists:locations,id',
            'image' => 'sometimes|nullable|image|max:2048',
            'translations' => 'sometimes|array',
            'translations.*.locale' => 'required_with:translations|string',
            'translations.*.name' => 'required_with:translations|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($limousine->image && File::exists(public_path($limousine->image))) {
                File::delete(public_path($limousine->image));
            }

            $file = $request->file('image');
            $filename = time() . '_limousine_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/limousines'), $filename);
            $validated['image'] = 'uploads/limousines/' . $filename;
        }

        $validated['updated_by'] = Auth::id();

        $limousine->update($validated);

        if ($request->has('translations')) {
            $limousine->translations()->delete();
            $translations = array_map(function ($t) {
                return [
                    ...$t,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];
            }, $request->translations);
            $limousine->translations()->createMany($translations);
        }

        return sendResponse(__('messages.updated_successfully'), 200, $limousine->load('translations', 'category', 'location'));
    }

    public function destroy($id)
    {
        $limousine = Limousine::find($id);

        if (!$limousine) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $limousine->deleted_by = Auth::id();
        $limousine->save();
        $limousine->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }

    public function trashed(Request $request)
    {
        $relationships = [
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
            'include_translations' => 'translations:limousine_id,locale,name,description',
            'include_category' => 'category:id,name',
            'include_location' => 'location:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($categoryId = $request->query('category_id')) {
                $query->where('category_id', $categoryId);
            }

            if ($locationId = $request->query('location_id')) {
                $query->where('location_id', $locationId);
            }

            if ($sortBy = $request->query('sort_by')) {
                $direction = $request->query('sort_direction', 'asc');
                $query->orderBy($sortBy, $direction);
            }
        };

        $data = $this->paginateResources($request, $relationships, 15, true, $queryModifier);

        return sendResponse(__('messages.retrieved_successfully'), 200, $data);
    }
}
