<?php

namespace Fjarfs\SrcService\Middleware;

use Closure;
use Carbon\Carbon;

class AccessKey
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
        if ($accessKey = $request->header('Access-Key')) {
            if ($secretKey = ayodecrypt($accessKey)) {
                $split = explode('@', $secretKey);

                if (count($split) == 2) {
                    if ($split[0] == config('app.key')) {
                        $currentTime = Carbon::now();
                        $requestTime = Carbon::createFromTimestamp($split[1]);

                        if ($requestTime->lessThanOrEqualTo($currentTime)) {
                            if ($currentTime->diffInSeconds($requestTime) <= 60) {
                                return $next($request);
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'Tidak ada otorisasi service'], 401);
    }
}
