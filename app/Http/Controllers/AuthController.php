<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{

	public function login(Request $request)
	{

		$request->validate([
			'email' => 'required|email',
			'password' => 'required',
		]);

		if (!Auth::attempt($request->only('email', 'password'))) {
			return sendResponse(__('messages.invalidCredentials'), 401);
		}

		/** @var User $user **/
		$user = Auth::guard()->user();
		$token = $user->createToken('token')->plainTextToken;

		return sendResponse(__('messages.loggedIn'), 200, [
			'token' => $token,
			'user' => $user,
		]);
	}

	public function register(Request $request)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|confirmed',
		]);

		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
		]);

		$token = $user->createToken('token')->plainTextToken;

		return sendResponse(__('messages.userCreated'), 201, [
			'token' => $token,
			'user' => $user,
		]);
	}

	public function getCurrentUser()
	{
		$user = Auth::user();
		return sendResponse(__('messages.authorized'), 200, $user);
	}
}
