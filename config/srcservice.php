<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the SRC Service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Hash
    |--------------------------------------------------------------------------
    |
    | This key is used by the SRC Service
    |
    */

    'hash' => 'sha256',

    /*
    |--------------------------------------------------------------------------
    | Expire
    |--------------------------------------------------------------------------
    |
    | The expire time is the number of seconds that the access key should be
    | considered valid. This security feature keeps access keys short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'expire' => 14400,

    /*
    |--------------------------------------------------------------------------
    | Cache Expire
    |--------------------------------------------------------------------------
    |
    | The cache expire time is the number of seconds.
    |
    */

    'cache_expire' => 600,

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    | Supported: "apc", "array", "database", "file", "memcached", "redis"
    |
    */

    'cache_driver' => env('CACHE_DRIVER', 'file')

];
