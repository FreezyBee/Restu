<?php

namespace FreezyBee\Restu\Http;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Class Resource
 * @package FreezyBee\Restu\Http
 */
class Resource extends Object
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var RequestException
     */
    private $exception;

    /**
     * @var int
     */
    private $status = 0;

    /**
     * @var float
     */
    private $timeRequest;

    /**
     * @var float
     */
    private $timeResponse;

    /**
     * Resource constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->timeRequest = microtime(true);
    }

    /**
     * @param Response $response
     */
    public function setSuccessResponse(Response $response)
    {
        $this->response = $response;
        $this->status = $response->getStatusCode();
        $this->timeResponse = microtime(true);

        try {
            $this->result = Json::decode($response->getBody()->getContents());
        } catch (JsonException $e) {
            // TODO
            $this->exception = $e;
        }
    }

    /**
     * @param GuzzleException $guzzleException
     */
    public function setErrorResponse(GuzzleException $guzzleException)
    {
        if ($guzzleException instanceof RequestException) {
            $this->exception = $guzzleException;
            $this->response = $response = $guzzleException->getResponse();

            try {
                $this->result = Json::decode($response->getBody()->getContents());
            } catch (JsonException $e) {
                $this->result = 'invalid result';
            }
            
            $this->status = $this->response->getStatusCode();
        } else {
            // TODO
            $this->exception = $guzzleException;
        }

        $this->timeResponse = microtime(true);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->timeResponse - $this->timeRequest;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return RequestException
     */
    public function getException()
    {
        return $this->exception;
    }
}
