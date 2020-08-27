<?php

namespace Fjarfs\SrcService;

use Closure;
use Illuminate\Http\Request;
use App\Libraries\Services\{
    AuthService,
    UserService
};
use Fjarfs\SrcService\Exception as ServiceException;

class Auth
{
    private const REQUEST_AUTH_INFO = 'serviceAuthInfo';
    private const REQUEST_AUTH_USER = 'serviceAuthUser';

    /**
     * Middleware auth service
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public static function middleware(Request $request, Closure $next)
    {
        $info = self::user();

        if (is_null($info)) :
            return response()->json(['message' => 'Tidak ada otorisasi'], 401);
        endif;

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

        try {
            $authService = AuthService::post('api/v1/auth/validate-token', [
                'token' => $token
            ]);

            ServiceException::on($authService);

            self::currentRequestSet(self::REQUEST_AUTH_INFO, $authService->data);

            return $authService->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get authorization header
     *
     * @return mixed
     */
    private static function getAuthorization()
    {
        return isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : false;
    }

    /**
     * Get token
     *
     * @param string $authorization
     * @return string
     */
    private static function getToken(string $authorization)
    {
        return substr($authorization, 7);
    }

    /**
     * Get auth user info
     *
     * @return mixed
     */
    public static function user()
    {
        if (self::currentRequestHas(self::REQUEST_AUTH_USER)) {
            return self::currentRequestGet(self::REQUEST_AUTH_USER);
        }

        $info = self::info();

        if (is_null($info)) :
            return null;
        endif;

        try {
            $user = UserService::get("api/v1/user/service/by-user-id/{$info->user_id}");

            ServiceException::on($user);

            self::currentRequestSet(self::REQUEST_AUTH_USER, $user->data);

            return $user->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * get auth user
     *
     * @return bool
     */
    public static function check()
    {
        $info = self::info();

        if (is_null($info)) :
            return false;
        endif;

        return true;
    }

    /**
     * Current request class
     *
     * @return Request
     */
    private static function currentRequest(): Request
    {
        return app('request');
    }

    /**
     * Is current request has property
     *
     * @param string $property
     * @return boolean
     */
    private static function currentRequestHas(string $property): bool
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
    private static function currentRequestSet(string $property, $data): void
    {
        self::currentRequest()->{self::currentRequestProperty($property)} = $data;
    }

    /**
     * Current request get property
     *
     * @param string $property
     * @return mixed
     */
    private static function currentRequestGet(string $property)
    {
        return self::currentRequest()->{self::currentRequestProperty($property)};
    }

    /**
     * Property name by current request
     *
     * @param string $property
     * @return string
     */
    private static function currentRequestProperty(string $property): string
    {
        return $property . '@' . self::currentRequest()->fingerprint();
    }
}
