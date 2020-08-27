<?php

namespace Fjarfs\SrcService;

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
            $message = $message ?: array_first($service->errors)[0];
            
            throw new \Exception($message);
        }
    }
}
