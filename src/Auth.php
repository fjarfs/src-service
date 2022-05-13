<?php

namespace Fjarfs\SrcService;

use Closure;
use Illuminate\Http\Request;
use App\Libraries\Services\{
    AuthService,
    UserService
};
use Fjarfs\SrcService\Exception as ServiceException;
use Illuminate\Support\Facades\Cache;

class Auth
{
    protected const REQUEST_AUTH_INFO = 'serviceAuthInfo';
    protected const REQUEST_AUTH_USER = 'serviceAuthUser';

    /**
     * Middleware auth service
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public static function middleware(Request $request, Closure $next)
    {
        $info = self::info();

        if (is_null($info)) {
            return response()->json(['message' => 'Tidak ada otorisasi'], 401);
        }

        return $next($request);
    }

    /**
     * Get auth info
     *
     * @return mixed
     */
    public static function info()
    {
        if (self::currentRequestHas(self::REQUEST_AUTH_INFO)) {
            return self::currentRequestGet(self::REQUEST_AUTH_INFO);
        }

        $auth   = self::getAuthorization();
        $token  = self::getToken($auth);

        $key = 'auth';
        if ($cache = self::cache($key)) {
            $data = $cache;
        } else {
            try {
                $authService = AuthService::post('api/v1/auth/validate-token', [
                    'token' => $token
                ]);

                ServiceException::on($authService);

                self::currentRequestSet(self::REQUEST_AUTH_INFO, $authService->data);

                $data = $authService->data;
            } catch (\Exception $e) {
                $data = null;
            }

            if ($data) {
                self::cachePut($key, $data);
            }
        }

        return $data;
    }

    /**
     * Get authorization header
     *
     * @return mixed
     */
    protected static function getAuthorization()
    {
        return isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : false;
    }

    /**
     * Get token
     *
     * @param string $authorization
     * @return string
     */
    protected static function getToken(string $authorization)
    {
        return substr($authorization, 7);
    }

    /**
     * Get key
     *
     * @param string $type
     * @param int $userId
     * @return string
     */
    protected static function getKey($type, $userId = null)
    {
        if ($userId) {
            return $type . '-' . hash('sha256', $userId);
        } else {
            $auth   = self::getAuthorization();
            $token  = self::getToken($auth);

            return $type . '-' . hash('sha256', $token);
        }
    }

    /**
     * Get auth user info
     *
     * @return mixed
     */
    public static function user()
    {
        if (self::currentRequestHas(self::REQUEST_AUTH_USER)) {
            $data = self::currentRequestGet(self::REQUEST_AUTH_USER);
        }

        $info = self::info();

        if (is_null($info)) {
            return null;
        }

        $key = ['user', $info->user_id];
        if ($cache = self::cache($key)) {
            $data = $cache;
        } else {
            try {
                $user = UserService::get("api/v1/user/service/by-user-id/{$info->user_id}");

                ServiceException::on($user);

                self::currentRequestSet(self::REQUEST_AUTH_USER, $user->data);

                $data = $user->data;
            } catch (\Exception $e) {
                $data = null;
            }

            if ($data) {
                self::cachePut($key, $data);
            }
        }

        return $data;
    }

    /**
     * get auth user
     *
     * @return bool
     */
    public static function check()
    {
        $info = self::info();

        if (is_null($info)) {
            return false;
        }

        return true;
    }

    /**
     * Get Cache
     *
     * @param string|array $key
     * @return mixed
     */
    protected static function cache($key)
    {
        $key = is_array($key) ? self::getKey(...$key) : self::getKey($key);

        return Cache::driver(self::cacheDriver())->get($key);
    }

    /**
     * Put Cache
     *
     * @param string|array $key
     * @param mixed $data
     * @return void
     */
    protected static function cachePut($key, $data)
    {
        $key = is_array($key) ? self::getKey(...$key) : self::getKey($key);

        Cache::driver(self::cacheDriver())->put($key, $data, config('srcservice.cache_expire'));
    }

    /**
     * Cache driver
     *
     * @return string|null
     */
    protected static function cacheDriver(): ?string
    {
        return config('srcservice.cache_driver');
    }

    /**
     * Current request class
     *
     * @return Request
     */
    protected static function currentRequest(): Request
    {
        return app('request');
    }

    /**
     * Is current request has property
     *
     * @param string $property
     * @return boolean
     */
    protected static function currentRequestHas(string $property): bool
    {
        return property_exists(self::currentRequest(), self::currentRequestProperty($property));
    }

    /**
     * Set current request property
     *
     * @param string $property
     * @param mixed $data
     * @return void
     */
    protected static function currentRequestSet(string $property, $data): void
    {
        self::currentRequest()->{self::currentRequestProperty($property)} = $data;
    }

    /**
     * Current request get property
     *
     * @param string $property
     * @return mixed
     */
    protected static function currentRequestGet(string $property)
    {
        return self::currentRequest()->{self::currentRequestProperty($property)};
    }

    /**
     * Property name by current request
     *
     * @param string $property
     * @return string
     */
    protected static function currentRequestProperty(string $property): string
    {
        $accessKey = app()->runningInConsole() ? hash('sha512', 'console') : self::currentRequest()->fingerprint();

        return $property . '@' . $accessKey;
    }
}
