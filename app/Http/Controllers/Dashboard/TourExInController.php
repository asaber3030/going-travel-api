<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\PaginateResources;
use Illuminate\Support\Facades\Auth;
use App\Models\TourExIn;

class TourExInController extends Controller
{
	use PaginateResources;

	protected $model = TourExIn::class;
	public function index(Request $request)
	{
		$relationships = [
			'include_tour' => 'tour:id,duration,price,type',
			'include_translations' => 'translations:tour_ex_in_id,locale,description',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where('title', 'like', "%{$search}%")
					->orWhereHas('translations', function ($q) use ($search) {
						$q->where('description', 'like', "%{$search}%");
					});
			}

			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
			}

			if ($type = $request->query('type')) {
				$query->where('type', $type);
			}

			if ($sortBy = $request->query('sort_by')) {
				$direction = $request->query('sort_direction', 'asc');
				$query->orderBy($sortBy, $direction);
			}
		};

		$data = $this->paginateResources($request, $relationships, 15, false, $queryModifier);

		return sendResponse(__('messages.retrieved_successfully'), 200, $data);
	}

	/**
	 * Create a new TourExIn record.
	 */
	public function store(Request $request)
	{
		$validated = $request->validate([
			'tour_id' => 'required|exists:tours,id',
			'title' => 'required|string|max:255',
			'type' => 'required|in:inclusion,exclusion',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.description' => 'required_with:translations|string',
		]);

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$exIn = TourExIn::create($validated);

		if ($request->has('translations')) {
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$exIn->translations()->createMany($translations);
		}

		return sendResponse(__('messages.created_successfully'), 201, $exIn->load('translations', 'tour'));
	}

	/**
	 * Retrieve a specific TourExIn record.
	 */
	public function show($id)
	{
		$exIn = TourExIn::with('translations', 'tour')->find($id);

		if (!$exIn) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $exIn);
	}

	/**
	 * Update an existing TourExIn record.
	 */
	public function update(Request $request, $id)
	{
		$exIn = TourExIn::find($id);

		if (!$exIn) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'tour_id' => 'sometimes|exists:tours,id',
			'title' => 'sometimes|string|max:255',
			'type' => 'sometimes|in:inclusion,exclusion',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.description' => 'required_with:translations|string',
		]);

		$validated['updated_by'] = Auth::id();

		$exIn->update($validated);

		if ($request->has('translations')) {
			$exIn->translations()->delete(); // Soft delete old translations
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$exIn->translations()->createMany($translations);
		}

		return sendResponse(__('messages.updated_successfully'), 200, $exIn->load('translations', 'tour'));
	}

	/**
	 * Soft delete a TourExIn record.
	 */
	public function destroy($id)
	{
		$exIn = TourExIn::find($id);

		if (!$exIn) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$exIn->deleted_by = Auth::id();
		$exIn->save();
		$exIn->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	/**
	 * Retrieve a paginated list of trashed TourExIn records.
	 */
	public function trashed(Request $request)
	{
		$relationships = [
			'include_tour' => 'tour:id,duration,price,type',
			'include_translations' => 'translations:tour_ex_in_id,locale,description',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where('title', 'like', "%{$search}%")
					->orWhereHas('translations', function ($q) use ($search) {
						$q->where('description', 'like', "%{$search}%");
					});
			}

			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
			}

			if ($type = $request->query('type')) {
				$query->where('type', $type);
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
		$exIn = TourExIn::onlyTrashed()->find($id);

		if (!$exIn) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$exIn->deleted_by = null;
		$exIn->updated_by = Auth::id();
		$exIn->save();
		$exIn->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $exIn->load('translations', 'tour'));
	}
}
