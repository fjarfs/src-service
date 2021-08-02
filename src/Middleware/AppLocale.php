<?php

namespace Fjarfs\SrcService\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

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
