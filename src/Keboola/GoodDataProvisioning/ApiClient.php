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
        $this->guzzleOptions['base_uri'] = substr($url, -1) == '/' ? $url : "$url/";
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

    public function getJob($id)
    {
        return $this->request('get', "jobs/$id");
    }

    public function updateJob($id, $params)
    {
        return $this->request('patch', "jobs/$id", $params);
    }

    public function getProject($id)
    {
        return $this->request('get', "projects/$id");
    }

    public function listProjects()
    {
        return $this->request('get', 'projects');
    }

    public function updateProject($id, $params)
    {
        return $this->request('patch', "projects/$id", $params);
    }

    public function getUser($id)
    {
        return $this->request('get', "users/$id");
    }

    public function updateUser($id, $params)
    {
        return $this->request('patch', "users/$id", $params);
    }

    public function listTokens()
    {
        return $this->request('get', 'tokens');
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
            $body = (string)$response->getBody();
            return $body? \GuzzleHttp\json_decode($body, true) : null;
        } catch (\Exception $e) {
            $response = $e instanceof RequestException && $e->hasResponse() ? $e->getResponse() : null;
            if ($response) {
                throw new UserException("Request $method $uri failed (status {$response->getStatusCode()}): {$response->getBody()}");
            }
            throw $e;
        }
    }
}
