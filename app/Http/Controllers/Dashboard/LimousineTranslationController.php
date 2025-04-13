<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LimousineTranslation;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LimousineTranslationController extends Controller
{
    use PaginateResources;

    protected $model = LimousineTranslation::class;

    public function index(Request $request)
    {
        $relationships = [
            'include_limousine' => 'limousine:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('name', 'like', "%{$search}%");
            }
            if ($limousineId = $request->query('limousine_id')) {
                $query->where('limousine_id', $limousineId);
            }
            if ($locale = $request->query('locale')) {
                $query->where('locale', $locale);
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
            'limousine_id' => 'required|exists:limousines,id',
            'locale' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $translation = LimousineTranslation::create($validated);

        return sendResponse(__('messages.created_successfully'), 201, $translation->load('limousine'));
    }

    public function show($id)
    {
        $translation = LimousineTranslation::with('limousine')->find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $translation);
    }

    public function update(Request $request, $id)
    {
        $translation = LimousineTranslation::find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'limousine_id' => 'sometimes|exists:limousines,id',
            'locale' => 'sometimes|string|max:10',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $translation->update($validated);

        return sendResponse(__('messages.updated_successfully'), 200, $translation->load('limousine'));
    }

    public function destroy($id)
    {
        $translation = LimousineTranslation::find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $translation->deleted_by = Auth::id();
        $translation->save();
        $translation->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }

    public function trashed(Request $request)
    {
        $relationships = [
            'include_limousine' => 'limousine:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('name', 'like', "%{$search}%");
            }
            if ($limousineId = $request->query('limousine_id')) {
                $query->where('limousine_id', $limousineId);
            }
            if ($locale = $request->query('locale')) {
                $query->where('locale', $locale);
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
        $translation = LimousineTranslation::onlyTrashed()->find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found_in_trash'), 404);
        }

        $translation->deleted_by = null;
        $translation->updated_by = Auth::id();
        $translation->save();
        $translation->restore();

        return sendResponse(__('messages.restored_successfully'), 200, $translation->load('limousine'));
    }
}
