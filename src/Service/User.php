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
     * @return bool
     * @throws \Exception
     */
    public function isRegistered($email)
    {
        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, 'is_registered']);
        $result = $this->api->call('POST', $endpoint, ['email' => $email], $this->language);
        return $result->registered;
    }
}
