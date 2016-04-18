<?php

namespace FreezyBee\Restu;

use FreezyBee\Restu\Http\Resource;
use FreezyBee\Restu\Service\BaseService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Class Api
 * @package FreezyBee\Restu
 */
class Api extends Object
{
    /** @var \Closure */
    public $onResponse;

    /**
     * @var array config
     */
    private $config;

    /**
     * @var array
     */
    private $services;

    /**
     * Api constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $bodyParams
     * @param null $language
     * @return mixed
     * @throws \Exception
     */
    public function call($method, $endpoint, array $bodyParams = [], $language = null)
    {
        $headers = ['X-Restu-Api-Key' => $this->config['apiKey']];

        if ($language) {
            $headers['Accept-Language'] = $language;
        }

        $uri = $this->config['apiUrl'] . $this->config['version'] . '/' . $endpoint;

        try {
            $body = $bodyParams ? Json::encode($bodyParams) : null;
        } catch (JsonException $e) {
            throw new \Exception('Restu request - invalid json', 666, $e);
        }
        $client = new Client;
        $request = new Request($method, $uri, $headers, $body);
        $resource = new Resource($request);

        try {
            /** @var \GuzzleHttp\Psr7\Response $response */
            $response = $client->send($request);
            $resource->setSuccessResponse($response);

        } catch (GuzzleException $e) {
            $resource->setErrorResponse($e);
        }

        $this->onResponse($resource);

        /** @var RequestException $exception */
        $exception = $resource->getException();

        if ($exception) {
            throw $exception;
        } else {
            return $resource->getResult();
        }
    }

    /**
     * @param array $pathArray
     * @param array $queryArray
     * @return string
     */
    public function generateEndpoint(array $pathArray, array $queryArray = [])
    {
        $uri = implode("/", $pathArray);

        if ($queryArray) {
            $uri .= '?' . http_build_query($queryArray);
        }

        return $uri;
    }

    /**
     * @param string $serviceType
     * @param string $serviceName
     * @param array $params
     * @return BaseService
     * @throws \Exception
     */
    public function createService($serviceType, $serviceName = '', array $params = [])
    {
        if ($serviceName == '') {
            $serviceName = $serviceType;
        }

        if (isset($this->services[$serviceName])) {
            throw new \Exception("Service with name $serviceName is allready registered id API");
        }

        if (!class_exists($serviceType) || !is_subclass_of($serviceType, BaseService::class)) {
            throw new \Exception("Invalid parameter \$serviceType - $serviceType");
        }

        return $this->services[$serviceName] = new $serviceType($this, $this->config, $params);
    }
}
