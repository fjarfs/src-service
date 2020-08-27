# SRC Service

**SRC Service** merupakan package yang digunakan oleh aplikasi SRC untuk request HTTP antar service dengan fitur authentication

### Cara Install

- `composer require fjarfs/src-service`

#### Laravel 

- Otomatis terdaftar oleh `Laravel Package Discovery`

#### Lumen

- Daftarkan service provider di `bootstrap/app.php`
	```php
	$app->register(Fjarfs\SrcService\SrcServiceProvider::class);
	```
  
### Cara Penggunaan
1. Buat folder pada path  `app/Libraries/Services`
2. Buat file 
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
3. Tambahkan `SERVICE_USER_URI` pada .env
    ```php
    SERVICE_USER_URI=http://localhost
    ```
4. Meminta request ke service user / `UserService`
    ```php
        use App\Libraries\Services\UserService;
        use Fjarfs\SrcService\Service\Exception as ServiceException;
    
        try {
            $userService = UserService::get(UserService::USER_BY_ID . "/1");
            ServiceException::on($userService);
            $user = $userService->data;
        } catch (\Exception $e) {
            $user = null;
        }
    ```
