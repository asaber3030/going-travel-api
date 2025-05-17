<?php

namespace App\Http\Controllers\UI;

use App\Models\ServiceCard;
use App\Http\Controllers\Controller;

class ServiceCardController extends Controller
{
	public function index()
	{
		try {
			$serviceCards = ServiceCard::with('translations')->get();

			return response()->json([
				'message' => __('messages.retrieved_successfully'),
				'data' => $serviceCards
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'message' => $e->getMessage(),
				'status' => 500
			], 500);
		}
	}

	public function show($id)
	{
		try {
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
		} catch (\Exception $e) {
			return response()->json([
				'message' => $e->getMessage(),
				'status' => 500
			], 500);
		}
	}
}
