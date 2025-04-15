<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\HotelTranslation;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelTranslationController extends Controller
{
    use PaginateResources;

    protected $model = HotelTranslation::class;

    // Get all translations for hotels with pagination
    public function index(Request $request)
    {
        $relationships = [
            'include_hotel' => 'hotel:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
        ];

        // Query modifier to handle filtering, searching, and sorting
        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('name', 'like', "%{$search}%");
            }
            if ($hotelId = $request->query('hotel_id')) {
                $query->where('hotel_id', $hotelId);
            }
            if ($locale = $request->query('locale')) {
                $query->where('locale', $locale);
            }
            if ($sortBy = $request->query('sort_by')) {
                $direction = $request->query('sort_direction', 'asc');
                $query->orderBy($sortBy, $direction);
            }
        };

        // Get paginated data
        $data = $this->paginateResources($request, $relationships, 15, false, $queryModifier);

        return sendResponse(__('messages.retrieved_successfully'), 200, $data);
    }

    // Store new translations for a hotel
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'locale' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'short_description' => 'nullable|string',
            'policy' => 'nullable|string',
            'slug' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $translation = HotelTranslation::create($validated);

        return sendResponse(__('messages.created_successfully'), 201, $translation->load('hotel'));
    }

    // Show a specific translation by ID
    public function show($id)
    {
        $translation = HotelTranslation::with('hotel')->find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $translation);
    }

    // Update an existing hotel translation
    public function update(Request $request, $id)
    {
        $translation = HotelTranslation::find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'hotel_id' => 'sometimes|exists:hotels,id',
            'locale' => 'sometimes|string|max:10',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'short_description' => 'nullable|string',
            'policy' => 'nullable|string',
            'slug' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $translation->update($validated);

        return sendResponse(__('messages.updated_successfully'), 200, $translation->load('hotel'));
    }

    // Delete a specific translation
    public function destroy($id)
    {
        $translation = HotelTranslation::find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $translation->deleted_by = Auth::id();
        $translation->save();
        $translation->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }

    // Get trashed (soft-deleted) translations
    public function trashed(Request $request)
    {
        $relationships = [
            'include_hotel' => 'hotel:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('name', 'like', "%{$search}%");
            }
            if ($hotelId = $request->query('hotel_id')) {
                $query->where('hotel_id', $hotelId);
            }
            if ($locale = $request->query('locale')) {
                $query->where('locale', $locale);
            }
            if ($sortBy = $request->query('sort_by')) {
                $direction = $request->query('sort_direction', 'asc');
                $query->orderBy($sortBy, $direction);
            }
        };

        $data = $this->paginateResources($request, $relationships, 15, true, $queryModifier);

        return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $data);
    }

    // Restore a trashed translation
    public function restore($id)
    {
        $translation = HotelTranslation::onlyTrashed()->find($id);

        if (!$translation) {
            return sendResponse(__('messages.not_found_in_trash'), 404);
        }

        $translation->deleted_by = null;
        $translation->updated_by = Auth::id();
        $translation->save();
        $translation->restore();

        return sendResponse(__('messages.restored_successfully'), 200, $translation->load('hotel'));
    }
}
