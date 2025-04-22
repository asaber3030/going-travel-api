<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ServiceCard;
use App\Models\ServiceCardTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class ServiceCardController extends Controller
{
	public function index()
	{
		$serviceCards = ServiceCard::with('translations')->get();

		return response()->json([
			'message' => __('messages.retrieved_successfully'),
			'data' => $serviceCards
		], 200);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'image' => 'nullable|image|max:1024',
			'key' => 'required|string|unique:service_cards,key',
			'enabled' => 'sometimes|boolean',
			'url' => 'nullable|string',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
			'translations.*.description' => 'nullable|string',
		]);

		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$filename = time() . '_service_card_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/service_cards'), $filename);
			$validated['image'] = 'uploads/service_cards/' . $filename;
		}

		$serviceCard = ServiceCard::create($validated);

		if ($request->has('translations')) {
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$serviceCard->translations()->createMany($translations);
		}

		return response()->json([
			'message' => __('messages.created_successfully'),
			'data' => $serviceCard->load('translations')
		], 201);
	}

	public function show($id)
	{
		$serviceCard = ServiceCard::with('translations')->find($id);

		if (!$serviceCard) {
			return response()->json([
				'message' => __('messages.not_found')
			], 404);
		}

		return response()->json([
			'message' => __('messages.retrieved_successfully'),
			'data' => $serviceCard
		], 200);
	}

	public function update(Request $request, $id)
	{
		$serviceCard = ServiceCard::find($id);

		if (!$serviceCard) {
			return response()->json([
				'message' => __('messages.not_found')
			], 404);
		}

		$validated = $request->validate([
			'image' => 'sometimes|nullable|image|max:1024',
			'key' => 'sometimes|string|unique:service_cards,key,' . $id,
			'enabled' => 'sometimes|boolean',
			'url' => 'sometimes|nullable|url',
			'translations' => 'sometimes|array',
			'translations.*.locale' => 'required_with:translations|string',
			'translations.*.title' => 'required_with:translations|string|max:255',
			'translations.*.description' => 'nullable|string',
		]);

		if ($request->hasFile('image')) {
			if ($serviceCard->image && File::exists(public_path($serviceCard->image))) {
				File::delete(public_path($serviceCard->image));
			}
			$file = $request->file('image');
			$filename = time() . '_service_card_' . $file->getClientOriginalName();
			$file->move(public_path('uploads/service_cards'), $filename);
			$validated['image'] = 'uploads/service_cards/' . $filename;
		}

		$serviceCard->update($validated);

		if ($request->has('translations')) {
			$serviceCard->translations()->delete();
			$translations = array_map(function ($translation) {
				$translation['created_by'] = Auth::id();
				$translation['updated_by'] = Auth::id();
				return $translation;
			}, $request->input('translations'));
			$serviceCard->translations()->createMany($translations);
		}

		return response()->json([
			'message' => __('messages.updated_successfully'),
			'data' => $serviceCard->load('translations')
		], 200);
	}

	public function destroy($id)
	{
		$serviceCard = ServiceCard::find($id);

		if (!$serviceCard) {
			return response()->json([
				'message' => __('messages.not_found')
			], 404);
		}

		if ($serviceCard->image && File::exists(public_path($serviceCard->image))) {
			File::delete(public_path($serviceCard->image));
		}

		$serviceCard->delete();

		return response()->json([
			'message' => __('messages.deleted_successfully')
		], 200);
	}
}
