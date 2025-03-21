<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\TourItinerary;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Controller;

class TourItineraryController extends Controller
{
	use PaginateResources;

	protected $model = TourItinerary::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_tour' => 'tour:id,duration,price,type,availability',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_translations' => 'translations:tour_itinerary_id,locale,title,description',
		];

		$queryModifier = function ($query, $request) {
			// Search by translation title or description
			if ($search = $request->query('search')) {
				$query->whereHas('translations', function ($q) use ($search) {
					$q->where('title', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			}

			// Filter by tour_id
			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
			}

			// Filter by day_number
			if ($dayNumber = $request->query('day_number')) {
				$query->where('day_number', $dayNumber);
			}

			// Sort by column
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
			'day_number' => 'required|integer|min:1',
			'image' => 'nullable|image|max:1024',
			'meals' => 'nullable|string',
			'overnight_location' => 'nullable|string',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
			'translations.*.description' => 'nullable|string',
		]);

		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$filename = time() . '_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tour_itineraries'), $filename);
			$validated['image'] = 'uploads/tour_itineraries/' . $filename;
		}

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$itinerary = TourItinerary::create($validated);

		// Handle translations if provided
		if ($request->has('translations')) {
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$itinerary->translations()->createMany($translations);
		}

		return sendResponse(__('messages.created_successfully'), 201, $itinerary->load('translations', 'tour'));
	}

	public function show($id)
	{
		$itinerary = TourItinerary::with('translations', 'tour')->find($id);

		if (!$itinerary) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $itinerary);
	}

	public function update(Request $request, $id)
	{
		$itinerary = TourItinerary::find($id);

		if (!$itinerary) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'tour_id' => 'sometimes|exists:tours,id',
			'day_number' => 'sometimes|integer|min:1',
			'image' => 'sometimes|nullable|image|max:2048',
			'meals' => 'sometimes|nullable|string',
			'overnight_location' => 'sometimes|nullable|string',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
			'translations.*.description' => 'nullable|string',
		]);

		if ($request->hasFile('image')) {
			if ($itinerary->image && File::exists(public_path($itinerary->image))) {
				File::delete(public_path($itinerary->image));
			}
			$file = $request->file('image');
			$filename = time() . '_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tour_itineraries'), $filename);
			$validated['image'] = 'uploads/tour_itineraries/' . $filename;
		}

		$validated['updated_by'] = Auth::id();

		$itinerary->update($validated);

		// Handle translations if provided
		if ($request->has('translations')) {
			$itinerary->translations()->delete(); // Soft delete old translations
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$itinerary->translations()->createMany($translations);
		}

		return sendResponse(__('messages.updated_successfully'), 200, $itinerary->load('translations', 'tour'));
	}

	public function destroy($id)
	{
		$itinerary = TourItinerary::find($id);

		if (!$itinerary) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$itinerary->deleted_by = Auth::id();
		$itinerary->save();
		$itinerary->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	public function trashed(Request $request)
	{
		$relationships = [
			'include_tour' => 'tour:id,duration,price,type,availability',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_translations' => 'translations:tour_itinerary_id,locale,title,description',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->whereHas('translations', function ($q) use ($search) {
					$q->where('title', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			}

			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
			}

			if ($dayNumber = $request->query('day_number')) {
				$query->where('day_number', $dayNumber);
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
		$itinerary = TourItinerary::onlyTrashed()->find($id);

		if (!$itinerary) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$itinerary->deleted_by = null;
		$itinerary->updated_by = Auth::id();
		$itinerary->save();
		$itinerary->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $itinerary->load('translations', 'tour'));
	}
}
