<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\LimousineReview;
use App\Traits\PaginateResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LimousineReviewController extends Controller
{
    use PaginateResources;

    protected $model = LimousineReview::class;

    public function index(Request $request)
    {
        $relationships = [
            'include_limousine' => 'limousine:id,name',
            'include_user' => 'user:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('comment', 'like', "%{$search}%");
            }
            if ($limousineId = $request->query('limousine_id')) {
                $query->where('limousine_id', $limousineId);
            }
            if ($rating = $request->query('rating')) {
                $query->where('rating', $rating);
            }
            if ($sortBy = $request->query('sort_by')) {
                $direction = $request->query('sort_direction', 'asc');
                $query->orderBy($sortBy, $direction);
            }
        };

        $data = $this->paginateResources($request, $relationships, 15, false, $queryModifier);

        return sendResponse(__('messages.retrieved_successfully'), 200, $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'limousine_id' => 'required|exists:limousines,id',
            'reviewer_name' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $limousineReview = LimousineReview::create($validated);

        return sendResponse(__('messages.created_successfully'), 201, $limousineReview);
    }

    public function show($id)
    {
        $limousineReview = LimousineReview::with('limousine', 'user')->find($id);

        if (!$limousineReview) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $limousineReview);
    }

    public function update(Request $request, $id)
    {
        $limousineReview = LimousineReview::find($id);

        if (!$limousineReview) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'reviewer_name' => 'sometimes|string',
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $limousineReview->update($validated);

        return sendResponse(__('messages.updated_successfully'), 200, $limousineReview);
    }

    public function destroy($id)
    {
        $limousineReview = LimousineReview::find($id);

        if (!$limousineReview) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $limousineReview->deleted_by = Auth::id();
        $limousineReview->save();
        $limousineReview->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }

    public function trashed(Request $request)
    {
        $relationships = [
            'include_limousine' => 'limousine:id,name',
            'include_user' => 'user:id,name',
            'include_created_by' => 'created_by:id,name',
            'include_updated_by' => 'updated_by:id,name',
            'include_deleted_by' => 'deleted_by:id,name',
        ];

        $queryModifier = function ($query, $request) {
            if ($search = $request->query('search')) {
                $query->where('comment', 'like', "%{$search}%");
            }
            if ($limousineId = $request->query('limousine_id')) {
                $query->where('limousine_id', $limousineId);
            }
        };

        $data = $this->paginateResources($request, $relationships, 15, true, $queryModifier);

        return sendResponse(__('messages.trashed_retrieved_successfully'), 200, $data);
    }

    public function restore($id)
    {
        $limousineReview = LimousineReview::onlyTrashed()->find($id);

        if (!$limousineReview) {
            return sendResponse(__('messages.not_found_in_trash'), 404);
        }

        $limousineReview->deleted_by = null;
        $limousineReview->updated_by = Auth::id();
        $limousineReview->save();
        $limousineReview->restore();

        return sendResponse(__('messages.restored_successfully'), 200, $limousineReview);
    }
}
