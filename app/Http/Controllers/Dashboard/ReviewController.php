<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Traits\PaginateResources;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Controller;

use App\Models\Review;

class ReviewController extends Controller
{
	use PaginateResources;

	protected $model = Review::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_tour' => 'tour:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where(function ($q) use ($search) {
					$q->where('client_name', 'like', "%{$search}%")
						->orWhere('title', 'like', "%{$search}%");
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

		$data = $this->paginateResources($request, $relationships, 15, false, $queryModifier);

		return sendResponse(__('messages.retrieved_successfully'), 200, $data);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'client_name' => 'required|string|max:255',
			'tour_id' => 'required|exists:tours,id',
			'rating' => 'required|numeric|min:1|max:5',
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'image' => 'nullable|image|max:2048',
		]);

		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$filename = time() . '_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/reviews'), $filename);
			$validated['image'] = 'uploads/reviews/' . $filename;
		}

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$review = Review::create($validated);

		return sendResponse(__('messages.created_successfully'), 201, $review);
	}

	public function show($id)
	{
		$review = Review::with('tour')->find($id);

		if (!$review) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $review);
	}

	public function update(Request $request, $id)
	{
		$review = Review::find($id);

		if (!$review) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'client_name' => 'sometimes|string|max:255',
			'tour_id' => 'sometimes|exists:tours,id',
			'rating' => 'sometimes|numeric|min:1|max:5',
			'title' => 'sometimes|string|max:255',
			'description' => 'nullable|string',
			'image' => 'sometimes|nullable|image|max:2048',
		]);

		if ($request->hasFile('image')) {
			if ($review->image && File::exists(public_path($review->image))) {
				File::delete(public_path($review->image));
			}
			$file = $request->file('image');
			$filename = time() . '_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/reviews'), $filename);
			$validated['image'] = 'uploads/reviews/' . $filename;
		}

		$validated['updated_by'] = Auth::id();

		$review->update($validated);

		return sendResponse(__('messages.updated_successfully'), 200, $review);
	}

	public function destroy($id)
	{
		$review = Review::find($id);

		if (!$review) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$review->deleted_by = Auth::id();
		$review->save();
		$review->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	public function trashed(Request $request)
	{
		$relationships = [
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_tour' => 'tour:id,name',
		];

		$queryModifier = function ($query, $request) {
			// Search by client name or title
			if ($search = $request->query('search')) {
				$query->where(function ($q) use ($search) {
					$q->where('client_name', 'like', "%{$search}%")
						->orWhere('title', 'like', "%{$search}%");
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
		$review = Review::onlyTrashed()->find($id);

		if (!$review) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$review->deleted_by = null;
		$review->updated_by = Auth::id();
		$review->save();
		$review->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $review);
	}
}
