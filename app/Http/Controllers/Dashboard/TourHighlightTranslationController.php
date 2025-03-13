<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TourHighlightTranslation;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourHighlightTranslationController extends Controller
{
	use PaginateResources;


	protected $model = TourHighlightTranslation::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_tour_highlight' => 'tour_highlight:id,tour_id,image',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where('title', 'like', "%{$search}%");
			}

			if ($tourHighlightId = $request->query('tour_highlight_id')) {
				$query->where('tour_highlight_id', $tourHighlightId);
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
			'tour_highlight_id' => 'required|exists:tour_highlights,id',
			'locale' => 'required|string',
			'title' => 'required|string|max:255',
		]);

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$translation = TourHighlightTranslation::create($validated);

		return sendResponse(__('messages.created_successfully'), 201, $translation->load('tour_highlight'));
	}

	public function show($id)
	{
		$translation = TourHighlightTranslation::with('tour_highlight')->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $translation);
	}

	public function update(Request $request, $id)
	{
		$translation = TourHighlightTranslation::find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'tour_highlight_id' => 'sometimes|exists:tour_highlights,id',
			'locale' => 'sometimes|string',
			'title' => 'sometimes|string|max:255',
		]);

		$validated['updated_by'] = Auth::id();

		$translation->update($validated);

		return sendResponse(__('messages.updated_successfully'), 200, $translation->load('tour_highlight'));
	}

	public function destroy($id)
	{
		$translation = TourHighlightTranslation::find($id);

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
			'include_tour_highlight' => 'tour_highlight:id,tour_id,image',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			// Search by title
			if ($search = $request->query('search')) {
				$query->where('title', 'like', "%{$search}%");
			}

			// Filter by tour_highlight_id
			if ($tourHighlightId = $request->query('tour_highlight_id')) {
				$query->where('tour_highlight_id', $tourHighlightId);
			}

			// Filter by locale
			if ($locale = $request->query('locale')) {
				$query->where('locale', $locale);
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

	public function restore($id)
	{
		$translation = TourHighlightTranslation::onlyTrashed()->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$translation->deleted_by = null;
		$translation->updated_by = Auth::id();
		$translation->save();
		$translation->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $translation->load('tour_highlight'));
	}
}
