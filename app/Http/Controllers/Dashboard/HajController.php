<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Haj;
use App\Models\HajCautions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HajController extends Controller
{
	public function index()
	{
		$haj = Haj::orderBy('id', 'desc')->paginate();
		return sendResponse('Haj', 200, $haj);
	}

	public function show($id)
	{
		$haj = Haj::with('days')->find($id);
		if (!$haj) return sendResponse(404, 'Haj not found');
		return sendResponse('Haj', 200, $haj);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string|max:1024',
			'long_description' => 'required|string',
			'price' => 'required|numeric',
			'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
			'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
			'hotel' => 'required|string|max:255',
			'meals' => 'required|string|max:255',
			'transportation_type' => 'required|string|max:255',
			'depature_date' => 'required|date',
			'return_date' => 'required|date',
			'notes' => 'required|string|max:1024',
			'cautions' => 'required|string',
		]);

		if ($request->hasFile('thumbnail')) {
			$validated['thumbnail'] = $this->uploadImage($request->file('thumbnail'), 'hajss');
		}

		if ($request->hasFile('banner')) {
			$validated['banner'] = $this->uploadImage($request->file('banner'), 'hajss');
		}

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$haj = Haj::create($validated);

		return sendResponse('Haj created successfully', 201, $haj);
	}

	public function update($id, Request $request)
	{
		$haj = Haj::find($id);

		if (!$haj) return sendResponse(__('messages.not_found'), 404);

		$validated = $request->validate([
			'title' => 'sometimes|string|max:255',
			'description' => 'sometimes|string|max:1024',
			'long_description' => 'sometimes|string',
			'price' => 'sometimes|numeric',
			'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
			'hotel' => 'sometimes|string|max:255',
			'meals' => 'sometimes|string|max:255',
			'transportation_type' => 'sometimes|string|max:255',
			'depature_date' => 'sometimes|date',
			'return_date' => 'sometimes|date',
			'notes' => 'sometimes|string|max:1024',
			'cautions' => 'sometimes|string',
		]);

		if ($request->hasFile('thumbnail')) {
			if ($haj->thumbnail && File::exists(public_path($haj->thumbnail))) {
				File::delete(public_path($haj->thumbnail));
			}
			$validated['thumbnail'] = $this->uploadImage($request->file('thumbnail'), 'hajss');
		}

		if ($request->hasFile('banner')) {
			if ($haj->banner && File::exists(public_path($haj->banner))) {
				File::delete(public_path($haj->banner));
			}
			$validated['banner'] = $this->uploadImage($request->file('banner'), 'hajss');
		}

		$haj->update($validated);

		return sendResponse(__('messages.updated_successfully'), 200, $haj);
	}

	public function destroy($id)
	{
		$haj = Haj::find($id);

		if (!$haj) {
			return sendResponse(__('messages.not_found'), 404);
		}

		$haj->deleted_by = Auth::id();
		$haj->save();
		$haj->delete();

		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	public function trashed(Request $request)
	{
		$data = Haj::withTrashed()->orderBy('deleted_by', 'desc')->paginate();
		return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $data);
	}

	public function restore($id)
	{
		$haj = Haj::onlyTrashed()->find($id);

		if (!$haj) {
			return sendResponse(__('messages.not_found_in_trash'), 404);
		}

		$haj->deleted_by = null;
		$haj->updated_by = Auth::id();
		$haj->save();
		$haj->restore();

		return sendResponse(__('messages.restored_successfully'), 200, $haj->load('translations', 'location', 'pickup_location', 'category'));
	}

	private function uploadImage($file, $folder)
	{
		$filename = time() . '_' . $file->getClientOriginalName();
		$file->move(public_path("uploads/$folder"), $filename);
		return "uploads/$folder/$filename";
	}
}
