<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if ($user = $request->user()) {
            $locale = $user->locale;
        }

        if (!$locale) {
            $locale = session('locale');
        }

        if (!$locale) {
            $locale = $request->getPreferredLanguage(['pt_BR', 'en']);
        }

        app()->setLocale($locale ?? config('app.fallback_locale', 'en'));

        return $next($request);
    }
}
