<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest();
        $query->where('id', '!=', Auth::user()->id);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $query->paginate(15));
    }

    public function allUsers()
    {
        $users = User::all();
        return sendResponse(__('messages.retrieved_successfully'), 200, $users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);


        $user = User::create($validated);

        return sendResponse(__('messages.created_successfully'), 201, $user);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return sendResponse(__('messages.not_found'), 404);
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => "sometimes|string|email|unique:users,email,$id",
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return sendResponse(__('messages.updated_successfully'), 200, $user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return sendResponse(__('messages.not_found'), 404);
        }

        $user->save();
        $user->delete();

        return sendResponse(__('messages.deleted_successfully'), 200);
    }

    public function trashed(Request $request)
    {
        $query = User::onlyTrashed()->latest();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        return sendResponse(__('messages.retrieved_successfully'), 200, $query->paginate(15));
    }
}
