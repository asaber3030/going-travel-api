<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LimousineOverview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaginateResources;

class LimousineOverviewController extends Controller
{
    use PaginateResources;

    protected $model = LimousineOverview::class;

    public function index(Request $request)
    {
        $relationships = [
            'include_limousine' => 'limousine:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('about_vehicle', 'like', "%{$search}%");
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
            'about_vehicle' => 'nullable|string',
            'key_features' => 'nullable|string',
            'available_services' => 'nullable|string',
            'pricing' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $overview = LimousineOverview::create($validated);

        return sendResponse(__('messages.created_successfully'), 201, $overview->load('limousine'));
    }

    public function show($id)
    {
        $overview = LimousineOverview::with('limousine')->find($id);

        if (!$overview) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $overview);
    }

    public function update(Request $request, $id)
    {
        $overview = LimousineOverview::find($id);

        if (!$overview) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'locale' => 'sometimes|string|max:10',
            'about_vehicle' => 'nullable|string',
            'key_features' => 'nullable|string',
            'available_services' => 'nullable|string',
            'pricing' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $overview->update($validated);

        return sendResponse(__('messages.updated_successfully'), 200, $overview->load('limousine'));
    }

    public function destroy($id)
    {
        $overview = LimousineOverview::find($id);

        if (!$overview) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $overview->deleted_by = Auth::id();
        $overview->save();
        $overview->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }

    public function trashed(Request $request)
    {
        $relationships = [
            'include_limousine' => 'limousine:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('about_vehicle', 'like', "%{$search}%");
            }
            if ($limousineId = $request->query('limousine_id')) {
                $query->where('limousine_id', $limousineId);
            }
            if ($locale = $request->query('locale')) {
                $query->where('locale', $locale);
            }
        };

        $data = $this->paginateResources($request, $relationships, 15, true, $queryModifier);

        return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $data);
    }

    public function restore($id)
    {
        $overview = LimousineOverview::onlyTrashed()->find($id);

        if (!$overview) {
            return sendResponse(__('messages.not_found_in_trash'), 404);
        }

        $overview->deleted_by = null;
        $overview->updated_by = Auth::id();
        $overview->save();
        $overview->restore();

        return sendResponse(__('messages.restored_successfully'), 200, $overview->load('limousine'));
    }
}
