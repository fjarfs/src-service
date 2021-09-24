<?php

namespace Fjarfs\SrcService\Middleware;

use Closure;
use Carbon\Carbon;
use Fjarfs\SrcService\Helpers\Security;

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
            if ($secretKey = Security::decrypt($accessKey)) {
                $split = explode('@', $secretKey);

                if (count($split) == 2 && $split[0] == config('srcservice.key')) {
                    $currentTime = Carbon::now();
                    $requestTime = Carbon::createFromTimestamp($split[1]);

                    if ($requestTime->lessThanOrEqualTo($currentTime) && $currentTime->diffInSeconds($requestTime) <= config('srcservice.expire')) {
                        return $next($request);
                    }
                }
            }
        }

        return response()->json(['message' => 'Tidak ada otorisasi service'], 401);
    }
}
