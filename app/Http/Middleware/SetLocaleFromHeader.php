<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocaleFromHeader
{

	public function handle(Request $request, Closure $next)
	{
		$locale = $request->header('Accept-Language');
		$locale = $locale ?: 'en';
		$supportedLocales = ['en', 'ar', 'fr', 'it', 'pt', 'de'];

		if (!in_array($locale, $supportedLocales)) {
			$locale = 'en';
		}

		App::setLocale($locale);
		return $next($request);
	}
}
