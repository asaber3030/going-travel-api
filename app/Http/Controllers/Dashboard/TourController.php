<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Tour;
use App\Models\TourItinerary;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\TourExIn;
use App\Models\TourHighlight;
use App\Models\TourImage;

class TourController extends Controller
{
	use PaginateResources;

	protected $model = Tour::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_translations' => 'translations:tour_id,locale,name,description,distance_description',
			'include_location' => 'location:id,name',
			'include_pickup_location' => 'pickup_location:id,name',
			'include_category' => 'category:id',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->whereHas('translations', function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			}

			if ($categoryId = $request->query('category_id')) {
				$query->where('category_id', $categoryId);
			}

			if ($locationId = $request->query('location_id')) {
				$query->where('location_id', $locationId);
			}

			if ($type = $request->query('type')) {
				$query->where('type', $type);
			}

			if ($availability = $request->query('availability')) {
				$query->where('availability', $availability);
			}

			if ($hasOffer = $request->query('has_offer')) {
				$query->where('has_offer', $hasOffer);
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
			'duration' => 'required|integer|min:1',
			'type' => 'required|string|in:private,public',
			'availability' => 'required|string',
			'banner' => 'nullable|image|max:1024',
			'thumbnail' => 'nullable|image|max:1024',
			'trip_information' => 'nullable|json',
			'before_you_go' => 'nullable|json',
			'max_people' => 'required|integer|min:1',
			'price_start' => 'required|numeric|min:0',
			'category_id' => 'required|exists:categories,id',
			'location_id' => 'required|exists:locations,id',
			'pickup_location_id' => 'required|exists:locations,id',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
			'translations.*.description' => 'nullable|string',
			'translations.*.distance_description' => 'nullable|string',
		]);

		if ($request->hasFile('banner')) {
			$file = $request->file('banner');
			$filename = time() . '_banner_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tours'), $filename);
			$validated['banner'] = 'uploads/tours/' . $filename;
		}

		if ($request->hasFile('thumbnail')) {
			$file = $request->file('thumbnail');
			$filename = time() . '_thumbnail_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tours'), $filename);
			$validated['thumbnail'] = 'uploads/tours/' . $filename;
		}

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$validated['location_id'] = (int)$validated['location_id'];
		$validated['pickup_location_id'] = (int)$validated['location_id'];
		$validated['category_id'] = (int)$validated['category_id'];

		$tour = Tour::create($validated);

		if ($request->has('translations')) {
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$tour->translations()->createMany($translations);
		}

		return sendResponse(__('messages.created_successfully'), 201, $tour->load('translations', 'location', 'pickup_location', 'category'));
	}

	public function show($id)
	{
		$tour = Tour::with('translations', 'location', 'pickup_location', 'category')->find($id);

		if (!$tour) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $tour);
	}

	public function update(Request $request, $id)
	{
		$tour = Tour::find($id);

		if (!$tour) return sendResponse(__('messages.not_found'), 404);

		$validated = $request->validate([
			'duration' => 'sometimes|integer|min:1',
			'price' => 'sometimes|numeric|min:0',
			'type' => 'sometimes|string|in:private,public',
			'availability' => 'sometimes|string',
			'banner' => 'sometimes|nullable|image|max:1024',
			'thumbnail' => 'sometimes|nullable|image|max:1024',
			'trip_information' => 'sometimes|nullable|string',
			'before_you_go' => 'sometimes|nullable|string',
			'max_people' => 'sometimes|integer|min:1',
			'price_start' => 'sometimes|numeric|min:0',
			'category_id' => 'sometimes|exists:categories,id',
			'location_id' => 'sometimes|exists:locations,id',
			'pickup_location_id' => 'sometimes|exists:locations,id',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
			'translations.*.description' => 'nullable|string',
			'translations.*.distance_description' => 'nullable|string',
		]);

		if ($request->hasFile('banner')) {
			if ($tour->banner && File::exists(public_path($tour->banner))) {
				File::delete(public_path($tour->banner));
			}
			$file = $request->file('banner');
			$filename = time() . '_banner_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tours'), $filename);
			$validated['banner'] = 'uploads/tours/' . $filename;
		}

		if ($request->hasFile('thumbnail')) {
			if ($tour->thumbnail && File::exists(public_path($tour->thumbnail))) {
				File::delete(public_path($tour->thumbnail));
			}
			$file = $request->file('thumbnail');
			$filename = time() . '_thumbnail_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tours'), $filename);
			$validated['thumbnail'] = 'uploads/tours/' . $filename;
		}

		$validated['updated_by'] = Auth::id();
		$validated['location_id'] = (int)$validated['location_id'];
		$validated['pickup_location_id'] = (int)$validated['location_id'];
		$validated['category_id'] = (int)$validated['category_id'];

		$tour->update($validated);

		if ($request->has('translations')) {
			$tour->translations()->delete();
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$tour->translations()->createMany($translations);
		}

		return sendResponse(__('messages.updated_successfully'), 200, $tour->load('translations', 'location', 'pickup_location', 'category'));
	}

	public function destroy($id)
	{
		$tour = Tour::find($id);

		if (!$tour) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$tour->deleted_by = Auth::id();
		$tour->save();
		$tour->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	public function trashed(Request $request)
	{
		$relationships = [
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_translations' => 'translations:tour_id,locale,name,description,distance_description',
			'include_location' => 'location:id,name',
			'include_pickup_location' => 'pickup_location:id,name',
			'include_category' => 'category:id',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->whereHas('translations', function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			}

			if ($categoryId = $request->query('category_id')) {
				$query->where('category_id', $categoryId);
			}

			if ($locationId = $request->query('location_id')) {
				$query->where('location_id', $locationId);
			}

			if ($type = $request->query('type')) {
				$query->where('type', $type);
			}

			if ($availability = $request->query('availability')) {
				$query->where('availability', $availability);
			}

			if ($hasOffer = $request->query('has_offer')) {
				$query->where('has_offer', $hasOffer);
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
		$tour = Tour::onlyTrashed()->find($id);

		if (!$tour) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$tour->deleted_by = null;
		$tour->updated_by = Auth::id();
		$tour->save();
		$tour->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $tour->load('translations', 'location', 'pickup_location', 'category'));
	}

	// Related Relations Data

	public function getTranslations($id)
	{
		$tour = Tour::find($id);
		if (!$tour) return sendResponse(__('messages.not_found'), 404);
		$translations = $tour->translations;
		return sendResponse(__('messages.translations_retrieved_successfully'), 200, $translations);
	}

	public function getHighlights($id)
	{
		$tour = Tour::find($id);
		if (!$tour) return sendResponse(__('messages.not_found'), 404);
		$highlights = TourHighlight::withTrashed()->orderBy('deleted_at', 'asc')->with('translations')->where('tour_id', $id)->get();
		return sendResponse(__('messages.highlights_retrieved_successfully'), 200, $highlights);
	}

	public function getItineraries($id)
	{
		$tour = Tour::find($id);
		if (!$tour) return sendResponse(__('messages.not_found'), 404);
		$itineraries = TourItinerary::withTrashed()->orderBy('deleted_at', 'asc')->with('translations')->where('tour_id', $id)->orderBy('day_number', 'asc')->get();
		return sendResponse(__('messages.itineraries_retrieved_successfully'), 200, $itineraries);
	}

	public function getInclusionsExclusions($id)
	{
		$tour = Tour::find($id);
		if (!$tour) return sendResponse(__('messages.not_found'), 404);
		$inclusionsExclusions = TourExIn::withTrashed()->orderBy('deleted_at', 'asc')->with('translations')->where('tour_id', $id)->get();
		return sendResponse(__('messages.inclusions_exclusions_retrieved_successfully'), 200, $inclusionsExclusions);
	}

	public function getImages($id)
	{
		$tour = Tour::find($id);
		if (!$tour) return sendResponse(__('messages.not_found'), 404);
		$images = TourImage::withTrashed()->orderBy('deleted_at', 'asc')->where('tour_id', $id)->get();
		return sendResponse(__('messages.images_retrieved_successfully'), 200, $images);
	}

	public function getReviews($id)
	{
		$tour = Tour::find($id);
		if (!$tour) return sendResponse(__('messages.not_found'), 404);
		$images = Review::withTrashed()->orderBy('deleted_at', 'asc')->where('tour_id', $id)->get();
		return sendResponse(__('messages.reviews_retrieved_successfully'), 200, $images);
	}
}
