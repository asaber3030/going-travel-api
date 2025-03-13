<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Traits\PaginateResources;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Controller;

use App\Models\TourHighlight;

class TourHighlightController extends Controller
{
	use PaginateResources;

	protected $model = TourHighlight::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_translations' => 'translations:tour_highlight_id,locale,title',
		];

		$queryModifier = function ($query, $request) {
			// Search by translation title
			if ($search = $request->query('search')) {
				$query->whereHas('translations', function ($q) use ($search) {
					$q->where('title', 'like', "%{$search}%");
				});
			}

			// Filter by tour_id
			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
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
			'image' => 'nullable|image|max:2048',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
		]);

		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$filename = time() . '_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tour_highlights'), $filename);
			$validated['image'] = 'uploads/tour_highlights/' . $filename;
		}

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$highlight = TourHighlight::create($validated);

		// Handle translations if provided
		if ($request->has('translations')) {
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$highlight->translations()->createMany($translations);
		}

		return sendResponse(__('messages.created_successfully'), 201, $highlight->load('translations'));
	}

	public function show($id)
	{
		$highlight = TourHighlight::with('translations')->find($id);

		if (!$highlight) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $highlight);
	}

	public function update(Request $request, $id)
	{
		$highlight = TourHighlight::find($id);

		if (!$highlight) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'tour_id' => 'sometimes|exists:tours,id',
			'image' => 'sometimes|nullable|image|max:2048',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
		]);

		if ($request->hasFile('image')) {
			if ($highlight->image && File::exists(public_path($highlight->image))) {
				File::delete(public_path($highlight->image));
			}
			$file = $request->file('image');
			$filename = time() . '_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/tour_highlights'), $filename);
			$validated['image'] = 'uploads/tour_highlights/' . $filename;
		}

		$validated['updated_by'] = Auth::id();

		$highlight->update($validated);

		if ($request->has('translations')) {
			$highlight->translations()->delete(); // Soft delete old translations
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$highlight->translations()->createMany($translations);
		}

		return sendResponse(__('messages.updated_successfully'), 200, $highlight->load('translations'));
	}

	public function destroy($id)
	{
		$highlight = TourHighlight::find($id);

		if (!$highlight) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$highlight->deleted_by = Auth::id();
		$highlight->save();
		$highlight->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	public function trashed(Request $request)
	{
		$relationships = [
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_translations' => 'translations:tour_highlight_id,locale,title',
		];

		$queryModifier = function ($query, $request) {
			// Search by translation title
			if ($search = $request->query('search')) {
				$query->whereHas('translations', function ($q) use ($search) {
					$q->where('title', 'like', "%{$search}%");
				});
			}

			if ($tourId = $request->query('tour_id')) {
				$query->where('tour_id', $tourId);
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
		$highlight = TourHighlight::onlyTrashed()->find($id);

		if (!$highlight) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$highlight->deleted_by = null;
		$highlight->updated_by = Auth::id();
		$highlight->save();
		$highlight->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $highlight->load('translations'));
	}
}
