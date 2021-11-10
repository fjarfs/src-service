<?php

namespace Fjarfs\SrcService;

use Illuminate\Support\Arr;

class Exception
{
    /**
     * Exception service
     *
     * @param \stdClass $service
     * @param string|null $message
     * @return void
     */
    public static function on(\stdClass $service, ?string $message = null)
    {
        if ($service->status == 'error') {
            $message = $message ?: Arr::first($service->errors)[0];

            throw new \Exception($message);
        }
    }
}
