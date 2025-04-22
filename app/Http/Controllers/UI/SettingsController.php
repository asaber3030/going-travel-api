<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
	public function index()
	{
		$settings = Settings::orderBy('updated_at', 'desc')->get();
		return sendResponse('Settings retrieved successfully', 200, $settings);
	}

	public function show(Request $request)
	{
		$settings = Settings::where('key', $request->key)->first();
		if (!$settings) return sendResponse('Settings not found', 404);
		return sendResponse('Settings retrieved successfully', 200, $settings);
	}

	public function get_by_group(string $group)
	{
		$settings = Settings::where('group', $group)->orderBy('updated_at', 'desc')->get();
		return sendResponse('Settings retrieved successfully', 200, $settings);
	}
}
