<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            app()->setLocale($user->locale ?? config('app.fallback_locale', 'en'));
        } else {
            $preferred = $request->getPreferredLanguage(['pt_BR', 'en']);
            app()->setLocale($preferred ?? config('app.fallback_locale', 'en'));
        }

        return $next($request);
    }
}
