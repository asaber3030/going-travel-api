<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LimousineFeature;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LimousineFeatureController extends Controller
{
    use PaginateResources;

    protected $model = LimousineFeature::class;

    public function index(Request $request)
    {
        $relationships = [
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('vehicle_features', 'like', "%{$search}%")
                    ->orWhere('additional_info', 'like', "%{$search}%");
            }
            if ($locale = $request->query('locale')) {
                $query->where('locale', $locale);
            }
            if ($limousineId = $request->query('limousine_id')) {
                $query->where('limousine_id', $limousineId);
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
            'limousine_id'     => 'required|exists:limousines,id',
            'locale'           => 'required|string|max:10',
            'vehicle_features' => 'nullable|string',
            'additional_info'  => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $feature = LimousineFeature::create($validated);

        return sendResponse(__('messages.created_successfully'), 201, $feature);
    }

    public function show($id)
    {
        $feature = LimousineFeature::with([
            'created_by:id,name',
            'updated_by:id,name',
            'deleted_by:id,name'
        ])->find($id);

        if (!$feature) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $feature);
    }

    public function update(Request $request, $id)
    {
        $feature = LimousineFeature::find($id);

        if (!$feature) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'locale'           => 'sometimes|string|max:10',
            'vehicle_features' => 'nullable|string',
            'additional_info'  => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $feature->update($validated);

        return sendResponse(__('messages.updated_successfully'), 200, $feature);
    }

    public function destroy($id)
    {
        $feature = LimousineFeature::find($id);

        if (!$feature) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $feature->deleted_by = Auth::id();
        $feature->save();
        $feature->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }

    public function trashed(Request $request)
    {
        $relationships = [
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('vehicle_features', 'like', "%{$search}%")
                    ->orWhere('additional_info', 'like', "%{$search}%");
            }
            if ($locale = $request->query('locale')) {
                $query->where('locale', $locale);
            }
            if ($limousineId = $request->query('limousine_id')) {
                $query->where('limousine_id', $limousineId);
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
        $feature = LimousineFeature::onlyTrashed()->find($id);

        if (!$feature) {
            return sendResponse(__('messages.not_found_in_trash'), 404);
        }

        $feature->deleted_by = null;
        $feature->updated_by = Auth::id();
        $feature->save();
        $feature->restore();

        return sendResponse(__('messages.restored_successfully'), 200, $feature);
    }
}
