# SRC Service

<p align="center">
<a href="https://packagist.org/packages/fjarfs/src-service"><img src="https://img.shields.io/packagist/dt/fjarfs/src-service" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/fjarfs/src-service"><img src="https://img.shields.io/packagist/v/fjarfs/src-service" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/fjarfs/src-service"><img src="https://img.shields.io/packagist/l/fjarfs/src-service" alt="License"></a>
</p>

**SRC Service** merupakan package yang digunakan oleh aplikasi SRC untuk request HTTP antar service dengan fitur authentication dan security access

### Versi yang Didukung
| Version | Laravel Version |
|---- |----|
| < 0.1.8 | <=5.6|
| > 0.1.9 | >=5.7 |


### Cara Install

- `composer require fjarfs/src-service`

    ##### Laravel 

    - Otomatis terdaftar oleh `Laravel Package Discovery`

    ##### Lumen

    - Daftarkan service provider di `bootstrap/app.php`
    	```php
    	$app->register(Fjarfs\SrcService\SrcServiceProvider::class);
    	```
### Setting Konfigurasi 
- Buat file `config/srcservice.php`
    ```php
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
    
        'expire' => 14400
    
    ];
    ```
- Daftarkan file konfigurasi
    #### Laravel
    - Otomatis terdaftar
    #### Lumen
    - Daftarkan file konfigurasi di `app/bootstrap.php`
    	```php
    	$app->configure('srcservice');
    	```
### Setting Middleware
- Pastikan setiap request antar service menggunakan middleware `service`
    #### Laravel
    - Daftarkan middleware service di `app/Http/Kernel.php`
        ```php
        /**
         * The application's route middleware.
         *
         * These middleware may be assigned to groups or used individually.
         *
         * @var array
         */
    	protected $routeMiddleware = [
            'service'   => \Fjarfs\SrcService\Middleware\Accesskey::class,
        ];
    	```
    #### Lumen
    - Daftarkan middleware service di `app/bootstrap.php`
        ```php
    	$app->routeMiddleware([
            'service'   => Fjarfs\SrcService\Middleware\Accesskey::class,
        ]);
    	```
- Contoh penggunaan middleware pada file `controller`
    ```php
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('service');
    }
	```
### Setting Service
1. Buat folder pada path  `app/Libraries/Services`
2. Buat file `UserService.php`
    ```php
    <?php

    namespace App\Libraries\Services;

    use Fjarfs\SrcService\Service;

    class UserService extends Service
    {
        /**
        * SERVICE_USER_URI
        */
        protected $baseUri = 'SERVICE_USER_URI';
        
        /**
        * Available URL
        */
        private const USER_BY_ID = 'api/v1/user/service/by-user-id';
    }
    ```
3. Buat file `AuthService.php`
    ```php
    <?php

    namespace App\Libraries\Services;

    use Fjarfs\SrcService\Service;

    class AuthService extends Service
    {
        /**
        * SERVICE_USER_URI
        */
        protected $baseUri = 'SERVICE_AUTH_URI';
    }
    ```
3. Tambahkan `SERVICE_USER_URI` pada .env, dan atur URI dari masing-masing service
    ```php
    SERVICE_USER_URI=http://localhost
    SERVICE_AUTH_URI=http://localhost
    ```

### Cara Penggunaan
* Cara request ke service user / `UserService`
    ```php
    use App\Libraries\Services\UserService;
    use Fjarfs\SrcService\Exception as ServiceException;
    
    try {
        $userService = UserService::get(UserService::USER_BY_ID . "/1");
        ServiceException::on($userService);
        $user = $userService->data;
    } catch (\Exception $e) {
        $user = null;
    }
    ```
