<?php

namespace App\Http\Controllers\Dashboard;

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

  public function update(string $key, Request $request)
  {
    $settings = Settings::where('key', $key)->first();
    if (!$settings) return sendResponse('Settings not found', 404);

    $request->validate([
      'value' => 'required|string',
      'locale' => 'required|string',
    ]);

    $settings->update($request->only('value', 'locale'));

    return sendResponse('Settings updated successfully', 200, $settings);
  }

  public function store(Request $request)
  {
    $request->validate([
      'key' => 'required|string|unique:settings',
      'value' => 'required|string',
      'locale' => 'required|string',
      'group' => 'required|string',
    ]);
    $settings = Settings::create($request->only('key', 'value', 'locale', 'group'));
    return sendResponse('Settings created successfully', 201, $settings);
  }
}
