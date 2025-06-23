<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\TourItineraryTranslation;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class TourItineraryTranslationController extends Controller
{
	use PaginateResources;

	protected $model = TourItineraryTranslation::class;


	public function index(Request $request)
	{
		$relationships = [
			'include_itinerary' => 'itinerary:id,tour_id,day_number',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
      $query->whereNull('deleted_at');
			if ($search = $request->query('search')) {
				$query->where(function ($q) use ($search) {
					$q->where('title', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			}

			if ($itineraryId = $request->query('itinerary_id')) {
				$query->where('itinerary_id', $itineraryId);
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
			'itinerary_id' => 'required|exists:tour_itineraries,id',
			'locale' => 'required|string',
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
		]);

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$translation = TourItineraryTranslation::create($validated);

		return sendResponse(__('messages.created_successfully'), 201, $translation->load('itinerary'));
	}


	public function show($id)
	{
		$translation = TourItineraryTranslation::with('itinerary') ->where('id', $id)
                               ->whereNull('deleted_at')
                               ->first();

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $translation);
	}

	public function update(Request $request, $id)
	{
		$translation = TourItineraryTranslation::find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'itinerary_id' => 'sometimes|exists:tour_itineraries,id',
			'locale' => 'sometimes|string',
			'title' => 'sometimes|string|max:255',
			'description' => 'sometimes|nullable|string',
		]);

		$validated['updated_by'] = Auth::id();

		$translation->update($validated);

		return sendResponse(__('messages.updated_successfully'), 200, $translation->load('itinerary'));
	}

	public function destroy($id)
	{
		$translation = TourItineraryTranslation::find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$translation->deleted_by = Auth::id();
		$translation->save();
		$translation->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}


	/* public function trashed(Request $request)
	{
		$relationships = [
			'include_itinerary' => 'itinerary:id,tour_id,day_number',
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

			if ($itineraryId = $request->query('itinerary_id')) {
				$query->where('itinerary_id', $itineraryId);
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
	} */


	public function restore($id)
	{
		$translation = TourItineraryTranslation::onlyTrashed()->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$translation->deleted_by = null;
		$translation->updated_by = Auth::id();
		$translation->save();
		$translation->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $translation->load('itinerary'));
	}
}
