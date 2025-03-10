<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
	public function home()
	{
		return view('home');
	}

	public function upload(Request $req)
	{
		$path = $req->file('file')->store('uploads');
		$publicPath = $req->file('file')->store('uploads', 'public');
		return response()->json([
			'message' => 'File uploaded successfully',
			'path' => $path,
			'publicPath' => $publicPath,
		]);
	}
}
