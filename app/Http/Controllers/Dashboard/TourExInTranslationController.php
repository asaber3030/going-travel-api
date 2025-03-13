<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\TourExInTranslation;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class TourExInTranslationController extends Controller
{
	use PaginateResources;

	protected $model = TourExInTranslation::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_exclusion' => 'exclusion:id,tour_id,title,type',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where('title', 'like', "%{$search}%");
			}
			if ($exclusionId = $request->query('exclusion_id')) {
				$query->where('exclusion_id', $exclusionId);
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
			'exclusion_id' => 'required|exists:inclusions_exclusions,id',
			'locale' => 'required|string',
			'title' => 'required|string|max:255',
		]);

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$translation = TourExInTranslation::create($validated);

		return sendResponse(__('messages.created_successfully'), 201, $translation->load('exclusion'));
	}


	public function show($id)
	{
		$translation = TourExInTranslation::with('exclusion')->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $translation);
	}

	public function update(Request $request, $id)
	{
		$translation = TourExInTranslation::find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'exclusion_id' => 'sometimes|exists:inclusions_exclusions,id',
			'locale' => 'sometimes|string',
			'title' => 'sometimes|string|max:255',
		]);

		$validated['updated_by'] = Auth::id();

		$translation->update($validated);

		return sendResponse(__('messages.updated_successfully'), 200, $translation->load('exclusion'));
	}

	public function destroy($id)
	{
		$translation = TourExInTranslation::find($id);

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
			'include_exclusion' => 'exclusion:id,tour_id,title,type',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where('title', 'like', "%{$search}%");
			}
			if ($exclusionId = $request->query('exclusion_id')) {
				$query->where('exclusion_id', $exclusionId);
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
		$translation = TourExInTranslation::onlyTrashed()->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$translation->deleted_by = null;
		$translation->updated_by = Auth::id();
		$translation->save();
		$translation->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $translation->load('exclusion'));
	}
}
