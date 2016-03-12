<?php

namespace FreezyBee\Restu\Service;

/**
 * Class User
 * @package FreezyBee\Restu\Service
 */
class User extends BaseService
{
    /**
     * Resource name
     */
    const RESOURCE_NAME = 'user';

    /**
     * @param $email
     * @return mixed
     * @throws \Exception
     */
    public function isRegistered($email)
    {
        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, 'is_registered']);
        return $this->api->call('POST', $endpoint, ['email' => $email]);
    }
}
