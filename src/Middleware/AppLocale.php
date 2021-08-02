<?php

namespace Fjarfs\SrcService\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

/**
 * Set App Locale By Query Params
 *
 * Use this middleware if you want setup the app locale
 * without authorization middleware.
 */
class AppLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->filled('locale')) {
            App::setLocale($request->input('locale', config('app.locale')));
        }

        return $next($request);
    }
}
