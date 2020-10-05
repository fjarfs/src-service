<?php

namespace Fjarfs\SrcService;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Fjarfs\SrcService\Helpers\Security;

class Service
{

    /**
     * Guzzle client
     *
     * @var Client
     */
    protected $client;

    /**
     * Base uri service from env
     *
     * @var string
     */
    protected $baseUri = 'SERVICE_URI';

    /**
     * Async service
     *
     * @var array
     */
    protected $asyncUri = [
        'SERVICE_LOG_URI'
    ];


    /**
     * Guzzle Response
     *
     * @var GuzzleResponse
     */
    public $response;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri'      => rtrim(env($this->baseUri, 'localhost'), '/') . '/',
            'headers'       => $this->getHeaders(),
            'http_errors'   => false
        ]);
    }

    /**
     * Get request header
     *
     * @param array $headers
     * @return array
     */
    protected function getHeaders()
    {
        $takes = collect([
            'Accept'        => 'accept',
            'Authorization' => 'authorization',
            'Access-From'   => 'service',
            'Access-Key'    => Security::encrypt(config('srcservice.key') . '@' . time())
        ]);

        $headers = $takes->transform(function ($item) {
            if ($item == 'authorization') {
                $item = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (app('request')->header('Authorization') ? app('request')->header('Authorization') : 'authorization');
            }
            if ($item == 'accept') {
                $item = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 'application/json';
            }
            
            return $item;
        })->filter(function ($item) {
            return $item != null;
        });
        
        return $headers->toArray();
    }

    /**
     * Call magic static
     *
     * @param string $method
     * @param array $args
     * @return class
     */
    public static function __callStatic($method, $args)
    {
        if (count($args) < 1) {
            throw new \InvalidArgumentException('Magic request methods require a URI and optional options array');
        }

        $uri    = $args[0];
        $opts   = isset($args[1]) ? $args[1] : [];

        if ($opts instanceof Collection) {
            $opts = $opts->toArray();
        } elseif (is_array($opts)) {
            $opts = ['form_params' => $opts];
        }

        $class      = get_called_class();
        $service    = new $class();

        $service->request($method, $uri, $opts);

        return $service->response();
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $uri
     * @param array $opts
     * @return void
     */
    public function request($method, $uri, $opts)
    {
        if (in_array($this->baseUri, $this->asyncUri)) {
            $this->response = $this->client->requestAsync($method, $uri, $opts)->wait();
        } else {
            $this->response = $this->client->request($method, $uri, $opts);
        }
    }

    /**
     * Get response json transformed
     *
     * @return \stdClass
     */
    public function response()
    {
        if (in_array($this->baseUri, $this->asyncUri)) {
            return [
                'status'    => 'success',
                'data'    => null
            ];
        } else {
            $response   = json_decode($this->response->getBody());
            $statusCode = $this->response->getStatusCode();

            if ($statusCode != 200) {
                $message = $this->response->getReasonPhrase();

                return (object) [
                    'status'    => 'error',
                    'code'      => $statusCode,
                    'message'   => $this->response->getReasonPhrase(),
                    'errors'    => optional($response)->errors
                        ? $response->errors
                        : (object) ['message' => [$message]]
                ];
            }

            return $response === null
                ? (string) $this->response->getBody()
                : $response;
        }
    }
}
