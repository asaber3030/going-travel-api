<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\LimousineService;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LimousineServiceController extends Controller
{
    use PaginateResources;

    protected $model = LimousineService::class;

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
                $query->where('our_services', 'like', "%{$search}%");
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
            'locale' => 'required|string',
            'our_services' => 'required|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $limousineService = LimousineService::create($validated);

        return sendResponse(__('messages.created_successfully'), 201, $limousineService);
    }

    public function show($id)
    {
        $limousineService = LimousineService::with('limousine')->find($id);

        if (!$limousineService) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $limousineService);
    }

    public function update(Request $request, $id)
    {
        $limousineService = LimousineService::find($id);

        if (!$limousineService) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'locale' => 'sometimes|string',
            'our_services' => 'sometimes|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $limousineService->update($validated);

        return sendResponse(__('messages.updated_successfully'), 200, $limousineService);
    }

    public function destroy($id)
    {
        $limousineService = LimousineService::find($id);

        if (!$limousineService) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $limousineService->deleted_by = Auth::id();
        $limousineService->save();
        $limousineService->delete();

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
                $query->where('our_services', 'like', "%{$search}%");
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
        $limousineService = LimousineService::onlyTrashed()->find($id);

        if (!$limousineService) {
            return sendResponse(__('messages.not_found_in_trash'), 404);
        }

        $limousineService->deleted_by = null;
        $limousineService->updated_by = Auth::id();
        $limousineService->save();
        $limousineService->restore();

        return sendResponse(__('messages.restored_successfully'), 200, $limousineService);
    }
}
