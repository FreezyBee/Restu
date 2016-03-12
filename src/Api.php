<?php

namespace FreezyBee\Restu;

use FreezyBee\Restu\Http\Resource;

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
     * Api constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $method
     * @param string $resource
     * @param int $id
     * @param string $subResource
     * @param array $parameters
     * @return mixed
     * @throws RestuException
     */
    public function call($method, $resource, $id = 0, $subResource = '', array $parameters = [])
    {
        $headers = ['X-Restu-Api-Key' => $this->config['apiKey']];
        $uri = $this->getApiUri($resource, $id, $subResource);

        try {
            $body = $parameters ? Json::encode($parameters) : null;
        } catch (JsonException $e) {
            throw new RestuException('Restu request - invalid json', 667, $e);
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
            throw new RestuException('Restu response: ' . $exception->getCode(), $exception->getCode(), $exception);
        } else {
            return $resource->getResult();
        }
    }

    /**
     * @param string $resource
     * @param int $id
     * @param string $subResource
     * @return string
     */
    public function getApiUri($resource, $id = 0, $subResource = '')
    {
        $uri = $this->config['apiUrl'] . $this->config['version'] . '/' . $resource;

        if ($id) {
            $uri .= '/' . $id;
        }

        if ($subResource) {
            $uri .= '/' . $subResource;
        }

        return $uri;
    }

    // TODO
    /**
     *
     */
    public function callService()
    {

    }
}
