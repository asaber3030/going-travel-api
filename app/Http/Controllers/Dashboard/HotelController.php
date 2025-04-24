<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\HotelTranslation;
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
			'category_id' => 'required|exists:categories,id',
			'price' => 'required|numeric',
			'stars' => 'required|numeric',
			'thumbnail' => 'required|image|max:2048',
			'banner' => 'required|image|max:4096',
			'translations' => 'required|array',
			'translations.*.locale' => 'required|string|max:10',
			'translations.*.name' => 'required|string|max:255',
			'translations.*.description' => 'required|string',
			'translations.*.address' => 'required|string',
			'translations.*.short_description' => 'required|string',
			'translations.*.policy' => 'required|string',
			'translations.*.slug' => 'required|string',
			'translations.*.room_types' => 'required|string',
			'amenity.*' => 'required|string',
			'amenity.free_wifi' => 'sometimes|string',
			'amenity.spa_wellness_center' => 'sometimes|string',
			'amenity.fitness_center' => 'sometimes|string',
			'amenity.gourmet_restaurant' => 'sometimes|string',
			'amenity.indoor_outdoor_pools' => 'sometimes|string',
			'amenity.air_conditioning' => 'sometimes|string',
			'amenity.flat_screen_tv' => 'sometimes|string',
			'amenity.free_parking' => 'sometimes|string',
			'amenity.front_desk_24h' => 'sometimes|string',
		]);

		if ($request->hasFile('thumbnail')) {
			$validated['thumbnail'] = $this->uploadImage($request->file('thumbnail'), 'hotels');
		}

		if ($request->hasFile('banner')) {
			$validated['banner'] = $this->uploadImage($request->file('banner'), 'hotels');
		}

		$translations = $validated['translations'];

		$validated['created_by'] = Auth::id();
		$validated['updated_by'] = Auth::id();

		$hotel = Hotel::create($validated);

		foreach ($translations as $key => $translation) {
			HotelTranslation::create([
				'hotel_id' => $hotel->id,
				'locale' => $translation['locale'],
				'name' => $translation['name'],
				'description' => $translation['description'] ?? null,
				'short_description' => $translation['short_description'] ?? null,
				'address' => $translation['address'] ?? null,
				'policy' => $translation['policy'] ?? null,
				'rooms_type' => $translation['room_types'] ?? null,
				'slug' => $translation['slug'] ?? null,

			]);
		}

		Amenity::create([
			'free_wifi' => $request->input('amenity.free_wifi') == 'yes' ? 1 : 0,
			'spa_wellness_center' => $request->input('amenity.spa_wellness_center') == 'yes' ? 1 : 0,
			'fitness_center' => $request->input('amenity.fitness_center') == 'yes' ? 1 : 0,
			'gourmet_restaurant' => $request->input('amenity.gourmet_restaurant') == 'yes' ? 1 : 0,
			'indoor_outdoor_pools' => $request->input('amenity.indoor_outdoor_pools') == 'yes' ? 1 : 0,
			'air_conditioning' => $request->input('amenity.air_conditioning') == 'yes' ? 1 : 0,
			'flat_screen_tv' => $request->input('amenity.flat_screen_tv') == 'yes' ? 1 : 0,
			'free_parking' => $request->input('amenity.free_parking') == 'yes' ? 1 : 0,
			'front_desk_24h' => $request->input('amenity.front_desk_24h') == 'yes' ? 1 : 0,
			'hotel_id' => $hotel->id
		]);

		return sendResponse(__('messages.created_successfully'), 201, $hotel->load('location', 'category', 'translations'));
	}

	public function show($id)
	{
		$hotel = Hotel::with('translations', 'amenity', 'location', 'category')->find($id);
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
			'thumbnail' => 'sometimes|image|max:2048',
			'banner' => 'sometimes|image|max:4096',
			'price' => 'sometimes|numeric',
			'stars' => 'sometimes|numeric',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'sometimes|string|max:10',
			'translations.*.name' => 'sometimes|string|max:255',
			'translations.*.description' => 'sometimes|string',
			'translations.*.address' => 'sometimes|string',
			'translations.*.short_description' => 'sometimes|string',
			'translations.*.room_types' => 'sometimes|string',
			'translations.*.policy' => 'sometimes|string',
			'translations.*.slug' => 'sometimes|string',

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

		if ($request->has('translations')) {
			$hotel->translations()->delete();
			foreach ($validated['translations'] as $key => $translation) {
				HotelTranslation::create([
					'hotel_id' => $hotel->id,
					'locale' => $translation['locale'],
					'name' => $translation['name'],
					'description' => $translation['description'] ?? null,
					'short_description' => $translation['short_description'] ?? null,
					'address' => $translation['address'] ?? null,
					'policy' => $translation['policy'] ?? null,
					'room_types' => $translation['room_types'] ?? null,
					'slug' => $translation['slug'] ?? null,
				]);
			}
		}

		$validated['updated_by'] = Auth::id();
		$hotel->update($validated);
		return sendResponse(__('messages.updated_successfully'), 200, $hotel->load('translations', 'amenity', 'location', 'category'));
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
