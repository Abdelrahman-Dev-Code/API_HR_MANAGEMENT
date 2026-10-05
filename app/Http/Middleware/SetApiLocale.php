<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language')
            ? $request->getPreferredLanguage(['ar', 'en'])
            : 'ar';

        app()->setLocale($locale ?? 'ar');

        return $next($request);
    }
}
