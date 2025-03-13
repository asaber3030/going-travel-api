<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\PaginateResources;
use App\Models\CategoryTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CategoryTranslationController extends Controller
{
	use PaginateResources;

	protected $model = CategoryTranslation::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_category' => 'category:id,image,created_by,updated_by,deleted_by',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			if ($search = $request->query('search')) {
				$query->where('name', 'like', "%{$search}%");
			}

			if ($categoryId = $request->query('category_id')) {
				$query->where('category_id', $categoryId);
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
			'category_id' => 'required|exists:categories,id',
			'locale' => 'required|string',
			'description' => 'required|string',
			'name' => 'required|string|max:255',
		]);

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$isTranslationExists = CategoryTranslation::where('category_id', $validated['category_id'])
			->where('locale', $validated['locale'])
			->first();

		if ($isTranslationExists) return sendResponse(__('messages.translation_exists'), 409);

		$translation = CategoryTranslation::create($validated);

		return sendResponse(__('messages.created_successfully'), 201, $translation->load('category'));
	}

	public function show($id)
	{
		$translation = CategoryTranslation::with('category')->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		return sendResponse(__('messages.retrieved_successfully'), 200, $translation);
	}

	public function update(Request $request, $id)
	{
		$translation = CategoryTranslation::find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$validated = $request->validate([
			'category_id' => 'sometimes|exists:categories,id',
			'locale' => 'sometimes|string',
			'description' => 'sometimes|string',
			'name' => 'sometimes|string|max:255',
		]);

		$validated['updated_by'] = Auth::id();

		$isTranslationExists = CategoryTranslation::where('category_id', $validated['category_id'])
			->where('locale', $validated['locale'])
			->where('id', '!=', $id)
			->first();

		if ($isTranslationExists) return sendResponse(__('messages.translation_exists'), 409);

		$translation->update($validated);

		return sendResponse(__('messages..updated_successfully'), 200, $translation->load('category'));
	}

	public function destroy($id)
	{
		$translation = CategoryTranslation::find($id);

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
			'include_category' => 'category:id,image,created_by,updated_by,deleted_by',
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
		];

		$queryModifier = function ($query, $request) {
			// Search by name
			if ($search = $request->query('search')) {
				$query->where('name', 'like', "%{$search}%");
			}

			// Filter by category_id
			if ($categoryId = $request->query('category_id')) {
				$query->where('category_id', $categoryId);
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
		$translation = CategoryTranslation::onlyTrashed()->find($id);

		if (!$translation) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$translation->deleted_by = null;
		$translation->updated_by = Auth::id();
		$translation->save();
		$translation->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $translation->load('category'));
	}
}
