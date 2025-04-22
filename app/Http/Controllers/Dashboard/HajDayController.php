<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Haj;
use App\Models\HajCautions;
use App\Models\HajDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HajDayController extends Controller
{

	public function show($id)
	{
		$haj = HajDay::find($id);
		if (!$haj) return sendResponse('Haj not found', 404);
		return sendResponse(200, 'Haj', $haj);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'haj_id' => 'required|exists:hajs,id',
			'title' => 'required|string|max:255',
			'description' => 'required|string|max:1024',
			'icon' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
		]);

		if ($request->hasFile('icon')) {
			$validated['icon'] = $this->uploadImage($request->file('icon'), 'hajss');
		}

		$hajDay = HajDay::create($validated);

		return sendResponse('Haj created successfully', 201, $hajDay);
	}

	public function update($id, Request $request)
	{
		$day = HajDay::find($id);

		if (!$day) return sendResponse(__('messages.not_found'), 404);

		$validated = $request->validate([
			'title' => 'sometimes|string|max:255',
			'description' => 'sometimes|string|max:1024',
			'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
		]);

		if ($request->hasFile('icon')) {
			if ($day->icon && File::exists(public_path($day->icon))) {
				File::delete(public_path($day->icon));
			}
			$validated['icon'] = $this->uploadImage($request->file('icon'), 'hajss');
		}

		$day->update($validated);
		return sendResponse(__('messages.updated_successfully'), 200, $day);
	}

	public function destroy($id)
	{
		$haj = HajDay::find($id);
		if (!$haj) return sendResponse(__('messages.not_found'), 404);
		$haj->delete();
		return sendResponse(__('messages.deleted_successfully'), 200);
	}

	public function trashed()
	{
		$data = HajDay::withTrashed()->orderBy('deleted_by', 'desc')->paginate();
		return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $data);
	}

	public function restore($id)
	{
		$haj = HajDay::onlyTrashed()->find($id);
		if (!$haj) return sendResponse(__('messages.not_found_in_trash'), 404);
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
