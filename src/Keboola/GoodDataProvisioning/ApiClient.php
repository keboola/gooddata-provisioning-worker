<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class ApiClient
{
    /**
     * Number of retries for one API call
     */
    const RETRIES_COUNT = 5;
    /**
     * Back off time before retrying API call
     */
    const BACKOFF_INTERVAL = 1;
    /**
     * Back off time before polling of async tasks
     */
    const WAIT_INTERVAL = 10;

    const DEFAULT_CLIENT_SETTINGS = [
        'timeout' => 600,
        'headers' => [
            'accept' => 'application/json',
            'content-type' => 'application/json; charset=utf-8'
        ]
    ];

    /** @var  \GuzzleHttp\Client */
    protected $guzzle;
    protected $guzzleOptions;
    /** @var  LoggerInterface */
    protected $logger;
    protected $token;

    protected $storageToken;

    public function __construct($url, $token, $logger = null, array $options = [])
    {
        $this->guzzleOptions = array_replace_recursive(self::DEFAULT_CLIENT_SETTINGS, $options);
        $this->guzzleOptions['base_uri'] = $url;
        if ($logger) {
            $this->logger = $logger;
        }
        $this->token = $token;
        $this->initClient();
    }

    protected function initClient()
    {
        $handlerStack = HandlerStack::create();

        /** @noinspection PhpUnusedParameterInspection */
        $handlerStack->push(Middleware::retry(
            function ($retries, RequestInterface $request, ResponseInterface $response = null, $error = null) {
                return $response && $response->getStatusCode() == 503;
            },
            function ($retries) {
                return rand(60, 600) * 1000;
            }
        ));
        /** @noinspection PhpUnusedParameterInspection */
        $handlerStack->push(Middleware::retry(
            function ($retries, RequestInterface $request, ResponseInterface $response = null, $error = null) {
                if ($retries >= self::RETRIES_COUNT) {
                    return false;
                } elseif ($response && $response->getStatusCode() > 499) {
                    return true;
                } elseif ($error) {
                    return true;
                } else {
                    return false;
                }
            },
            function ($retries) {
                return (int) pow(2, $retries - 1) * 1000;
            }
        ));

        $this->guzzle = new \GuzzleHttp\Client(array_merge(['handler' => $handlerStack], $this->guzzleOptions));
    }

    public function getProjectJob($id)
    {
        return $this->request('get', "/projects/jobs/$id");
    }

    public function updateProjectJob($id, $params)
    {
        return $this->request('patch', "/projects/jobs/$id", $params);
    }

    public function getUserJob($id)
    {
        return $this->request('get', "/users/jobs/$id");
    }

    public function updateUserJob($id, $params)
    {
        return $this->request('patch', "/users/jobs/$id", $params);
    }

    public function request($method, $uri, $params = [])
    {
        $options = ['headers' => ['X-KBC-ManageApiToken' => $this->token]];
        if ($params) {
            if ($method == 'GET' || $method == 'DELETE') {
                $options['query'] = $params;
            } else {
                $options['json'] = $params;
            }
        }

        try {
            $response = $this->guzzle->request($method, $uri, $options);
            return \GuzzleHttp\json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $response = $e instanceof RequestException && $e->hasResponse() ? $e->getResponse() : null;
            if ($response) {
                $responseJson = \GuzzleHttp\json_decode($response->getBody(), true);
                throw new UserException("Request $method  $uri failed (status {$response->getStatusCode()}): $responseJson");
            }
            throw $e;
        }
    }
}
