<?php

namespace FreezyBee\Restu\Service;

use FreezyBee\Restu\Api;
use Nette\Object;

/**
 * Class BaseService
 * @package FreezyBee\Restu\Service
 */
abstract class BaseService extends Object
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * BaseService constructor.
     * @param Api $api
     * @param array $config
     * @param array $params
     */
    public function __construct(Api $api, array $config, array $params)
    {
        $this->api = $api;
    }
}
