<?php

namespace FreezyBee\Restu;

use GuzzleHttp\Exception\RequestException;

/**
 * Base RestuException
 */
class RestuException extends \Exception
{
    /** @var \stdClass */
    protected $info;

    /**
     * RestuException constructor.
     * @param string $data
     * @param RequestException $previous
     */
    public function __construct($data, RequestException $previous)
    {
        $message = (isset($data->developer_message)) ? $data->developer_message : 'Unknown';
        $message .= (isset($data->localized_user_message)) ? ' (' . $data->localized_user_message . ')': '';
        $this->info = $data;

        parent::__construct($message, $previous->getCode(), $previous);
    }

    /**
     * @return \stdClass|string
     */
    public function getInfo()
    {
        return $this->info;
    }
}

/**
 * Base RestuException
 */
class MissingParameterException extends RestuException
{
    protected $code = 400;
}

class AuthenticationException extends RestuException
{
    protected $code = 400;
}

class BadRequestException extends RestuException
{
    protected $code = 404;
}

class ServerException extends RestuException
{
    protected $code = 500;
}
