<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Traits\PaginateResources;

class HotelController extends Controller
{
	use PaginateResources;

	protected $model = Hotel::class;

	public function index(Request $request)
	{
		$relationships = [
			'include_created_by' => 'created_by:id,name',
			'include_updated_by' => 'updated_by:id,name',
			'include_deleted_by' => 'deleted_by:id,name',
			'include_location' => 'location:id,name',
			'include_category' => 'category:id,name',
			'include_translations' => 'translations:hotel_id,locale,name',
		];

		$data = $this->paginateResources($request, $relationships);

		return sendResponse(__('messages.retrieved_successfully'), 200, $data);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'location_id' => 'required|exists:locations,id',
			'category_id' => 'nullable|exists:categories,id',
			'thumbnail' => 'nullable|image|max:2048',
			'banner' => 'nullable|image|max:4096',
		]);

		if ($request->hasFile('thumbnail')) {
			$validated['thumbnail'] = $this->uploadImage($request->file('thumbnail'), 'hotels');
		}

		if ($request->hasFile('banner')) {
			$validated['banner'] = $this->uploadImage($request->file('banner'), 'hotels');
		}

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$hotel = Hotel::create($validated);

		return sendResponse(__('messages.created_successfully'), 201, $hotel->load('location', 'category', 'translations'));
	}

	public function show($id)
	{
		$hotel = Hotel::with('translations', 'location', 'category')->find($id);

		if (!$hotel) return sendResponse(__('messages.not_found'), 404);

		return sendResponse(__('messages.retrieved_successfully'), 200, $hotel);
	}

	public function update(Request $request, $id)
	{
		$hotel = Hotel::find($id);
		if (!$hotel) return sendResponse(__('messages.not_found'), 404);

		$validated = $request->validate([
			'location_id' => 'sometimes|exists:locations,id',
			'category_id' => 'sometimes|exists:categories,id',
			'thumbnail' => 'nullable|image|max:2048',
			'banner' => 'nullable|image|max:4096',
		]);

		if ($request->hasFile('thumbnail')) {
			if ($hotel->thumbnail && File::exists(public_path($hotel->thumbnail))) {
				File::delete(public_path($hotel->thumbnail));
			}
			$validated['thumbnail'] = $this->uploadImage($request->file('thumbnail'), 'hotels');
		}

		if ($request->hasFile('banner')) {
			if ($hotel->banner && File::exists(public_path($hotel->banner))) {
				File::delete(public_path($hotel->banner));
			}
			$validated['banner'] = $this->uploadImage($request->file('banner'), 'hotels');
		}

		$validated['updated_by'] = Auth::id();

		$hotel->update($validated);

		return sendResponse(__('messages.updated_successfully'), 200, $hotel->load('translations', 'location', 'category'));
	}

	public function destroy($id)
	{
		$hotel = Hotel::find($id);
		if (!$hotel) return sendResponse(__('messages.not_found'), 404);

		$hotel->deleted_by = Auth::id();
		$hotel->save();
		$hotel->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	private function uploadImage($file, $folder)
	{
		$filename = time() . '_' . $file->getClientOriginalName();
		$file->move(public_path("uploads/$folder"), $filename);
		return "uploads/$folder/$filename";
	}
}
