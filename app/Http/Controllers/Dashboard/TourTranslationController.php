<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourTranslation;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaginateResources;

class TourTranslationController extends Controller
{
	use PaginateResources;

	private $model = TourTranslation::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_tour' => 'tour:id,duration,price,type,availability,category_id,location_id,pickup_location_id',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where(function ($q) use ($search) {
					$q->where('title', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			}

			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
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
			'tour_id' => 'required|exists:tours,id',
			'locale' => 'required|string',
			'title' => 'required|string|max:255',
			'distance_description' => 'nullable|string',
			'description' => 'nullable|string',
			'location' => 'nullable|string',
			'pickup_location' => 'nullable|string',
		]);

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$isTourTranslationExists = TourTranslation::where('tour_id', $validated['tour_id'])
			->where('locale', $validated['locale'])
			->exists();

		if ($isTourTranslationExists) return sendResponse(__('messages.already_exists'), 409);

		$translation = TourTranslation::create($validated);

		return sendResponse(__('messages.created_successfully'), 201, $translation->load('tour'));
	}

	public function show($id)
	{
		$translation = TourTranslation::with('tour')->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $translation);
	}

	public function update(Request $request, $id)
	{
		$translation = TourTranslation::find($id);

		if (!$translation) return sendResponse(__('messages.not_found'), 404);

		$validated = $request->validate([
			'tour_id' => 'sometimes|exists:tours,id',
			'locale' => 'sometimes|string',
			'title' => 'sometimes|string|max:255',
			'distance_description' => 'sometimes|nullable|string',
			'description' => 'sometimes|nullable|string',
			'location' => 'sometimes|nullable|string',
			'pickup_location' => 'sometimes|nullable|string',
		]);

		$isTourTranslationExists = TourTranslation::where('tour_id', $validated['tour_id'])
			->where('locale', $validated['locale'])
			->where('id', '!=', $id)
			->exists();

		if ($isTourTranslationExists) return sendResponse(__('messages.already_exists'), 409);
		$validated['updated_by'] = Auth::id();

		$translation->update($validated);

		return sendResponse(__('messages.updated_successfully'), 200, $translation->load('tour'));
	}

	public function destroy($id)
	{
		$translation = TourTranslation::find($id);

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
			'include_tour' => 'tour:id,duration,price,type,availability,category_id,location_id,pickup_location_id',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where(function ($q) use ($search) {
					$q->where('title', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			}

			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
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
		$translation = TourTranslation::onlyTrashed()->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$translation->deleted_by = null;
		$translation->updated_by = Auth::id();
		$translation->save();
		$translation->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $translation->load('tour'));
	}
}
