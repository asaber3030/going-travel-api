<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ServiceCard;
use App\Models\ServiceCardTranslation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceCardTranslationController extends Controller
{
	public function store(Request $request)
	{
		$validated = $request->validate([
			'service_card_id' => 'required|exists:service_cards,id',
			'locale' => 'required|string',
			'title' => 'required|string|max:255',
			'description' => 'nullable|string'
		]);

		$translation = ServiceCardTranslation::create($validated);

		return response()->json([
			'message' => __('messages.created_successfully'),
			'data' => $translation
		], 201);
	}

	public function update(Request $request, $id)
	{
		$translation = ServiceCardTranslation::where('service_card_id', $id)->find($id);

		if (!$translation) {
			return response()->json([
				'message' => __('messages.not_found')
			], 404);
		}

		$validated = $request->validate([
			'locale' => 'sometimes|string',
			'title' => 'sometimes|string|max:255',
			'description' => 'sometimes|nullable|string'
		]);

		$translation->update($validated);

		return response()->json([
			'message' => __('messages.updated_successfully'),
			'data' => $translation
		], 200);
	}
}
