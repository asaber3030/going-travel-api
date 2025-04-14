<?php

namespace App\Http\Controllers\UI;

use App\Models\Hotel;
use App\Models\HotelTranslation;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class HotelController extends Controller
{
    use PaginateResources;

    protected $model = Hotel::class;

    // Get list of hotels with basic data
    public function index(Request $request)
    {
        $query = Hotel::query();
        $take = $request->query('take', 6);
        $orderBy = $request->query('order_by', 'id');
        $order = $request->query('order', 'desc');

        $hotels = $query
            ->select('id', 'status', 'location_id', 'category_id', 'stars', 'thumbnail', 'banner')
            ->orderBy($orderBy, $order)
            ->with('location') // Assuming a relation exists for location
            ->take($take)
            ->get();

        return sendResponse(__('messages.retrieved_successfully'), 200, $hotels);
    }

    // Get paginated list of hotels with optional filters
    public function paginated(Request $request)
    {
        $query = Hotel::query();
        $take = $request->query('take', 12);
        $search = $request->query('search');
        $orderBy = $request->query('order_by', 'id');
        $order = $request->query('order', 'desc');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%'); // Searching by hotel name
        }

        $hotels = $query
            ->select('id', 'status', 'location_id', 'category_id', 'stars', 'thumbnail', 'banner')
            ->orderBy($orderBy, $order)
            ->with('location') // Assuming a relation exists for location
            ->paginate($take);

        return sendResponse(__('messages.retrieved_successfully'), 200, $hotels);
    }

    // Show a single hotel with its translations
    public function show($id)
    {
        $hotel = Hotel::with([
            'translations' => fn($q) => $q->select('id', 'hotel_id', 'locale', 'name', 'description', 'short_description', 'address', 'policy', 'room_types', 'slug'),
            'location' => fn($q) => $q->select('id', 'name'), // Assuming location has a name field
        ])->find($id);

        if (!$hotel) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $hotel);
    }

    // Store a new hotel and its translations
    public function store(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|boolean',
            'location_id' => 'required|exists:locations,id',
            'category_id' => 'required|exists:categories,id',
            'stars' => 'required|integer',
            'thumbnail' => 'nullable|string',
            'banner' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:10',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.short_description' => 'nullable|string',
            'translations.*.address' => 'nullable|string',
            'translations.*.policy' => 'nullable|string',
            'translations.*.room_types' => 'nullable|string',
            'translations.*.slug' => 'nullable|string',
        ]);

        $hotel = Hotel::create($validated);

        $translations = array_map(function ($t) use ($hotel) {
            return [
                'hotel_id' => $hotel->id,
                'locale' => $t['locale'],
                'name' => $t['name'],
                'description' => $t['description'],
                'short_description' => $t['short_description'],
                'address' => $t['address'],
                'policy' => $t['policy'],
                'room_types' => $t['room_types'],
                'slug' => $t['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $validated['translations']);

        HotelTranslation::insert($translations);

        return sendResponse(__('messages.created_successfully'), 201, $hotel->load('translations'));
    }

    // Update an existing hotel and its translations
    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'status' => 'sometimes|boolean',
            'location_id' => 'sometimes|exists:locations,id',
            'category_id' => 'sometimes|exists:categories,id',
            'stars' => 'sometimes|integer',
            'thumbnail' => 'nullable|string',
            'banner' => 'nullable|string',
            'translations' => 'sometimes|array',
            'translations.*.locale' => 'required|string|max:10',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.short_description' => 'nullable|string',
            'translations.*.address' => 'nullable|string',
            'translations.*.policy' => 'nullable|string',
            'translations.*.room_types' => 'nullable|string',
            'translations.*.slug' => 'nullable|string',
        ]);

        $hotel->update($validated);

        // Update translations
        if (isset($validated['translations'])) {
            $translations = array_map(function ($t) use ($hotel) {
                return [
                    'hotel_id' => $hotel->id,
                    'locale' => $t['locale'],
                    'name' => $t['name'],
                    'description' => $t['description'],
                    'short_description' => $t['short_description'],
                    'address' => $t['address'],
                    'policy' => $t['policy'],
                    'room_types' => $t['room_types'],
                    'slug' => $t['slug'],
                    'updated_at' => now(),
                ];
            }, $validated['translations']);

            HotelTranslation::where('hotel_id', $hotel->id)->delete();
            HotelTranslation::insert($translations);
        }

        return sendResponse(__('messages.updated_successfully'), 200, $hotel->load('translations'));
    }

    // Delete a hotel and its translations
    public function destroy($id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $hotel->translations()->delete(); // Deleting all translations
        $hotel->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }
}
