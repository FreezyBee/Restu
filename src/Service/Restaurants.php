<?php

namespace FreezyBee\Restu\Service;

use FreezyBee\Restu\Api;

/**
 * Class Restaurants
 * @package FreezyBee\Restu\Service
 */
class Restaurants extends BaseService
{
    /**
     * Resource name
     */
    const RESOURCE_NAME = 'restaurants';

    /**
     * Kurak
     */
    const SMOKING_YES = 2;

    /**
     * Nekurak
     */
    const SMOKING_NO = 1;

    /**
     * @var int
     */
    private $id;

    /**
     * Restaurants constructor.
     * @param Api $api
     * @param array $config
     * @param array $params
     * @throws \Exception
     */
    public function __construct(Api $api, array $config, array $params)
    {
        if (empty($params['id']) && !$config['restaurantId']) {
            throw new \Exception('Missing parameter id - you must define it in config
            file or in addService $params argument');
        }

        $this->id = (!empty($params['id'])) ? $params['id'] : $config['restaurantId'];

        parent::__construct($api, $config, $params);
    }

    /**
     * @return \stdClass
     */
    public function getDetail()
    {
        return $this->api->call('GET', self::RESOURCE_NAME, $this->id);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getReviews($limit = 0, $offset = 0)
    {
        $params = [];

        if ($limit) {
            $params['limit'] = $limit;
        }

        if ($offset) {
            $params['offset'] = $offset;
        }

        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, $this->id, 'reviews'], $params);
        return $this->api->call('GET', $endpoint);
    }


    /**
     * @return \stdClass
     */
    public function getDailyMenus()
    {
        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, $this->id, 'daily_menus']);
        return $this->api->call('GET', $endpoint);
    }

    /**
     * @return \stdClass
     */
    public function getMenus()
    {
        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, $this->id, 'menus']);
        return $this->api->call('GET', $endpoint);
    }

    /**
     * @param $menuId
     * @return \stdClass
     */
    public function getMenuDetail($menuId)
    {
        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, $this->id, 'menus', $menuId]);
        return $this->api->call('GET', $endpoint);
    }

    /**
     * @return \stdClass
     */
    public function getReservationDefaultTerms()
    {
        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, $this->id, 'reservation_terms']);
        return $this->api->call('GET', $endpoint);
    }

    /**
     * @param \DateTime $date
     * @return \stdClass
     */
    public function getReservationTerms(\DateTime $date)
    {
        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, $this->id, 'reservation_terms']);
        return $this->api->call('POST', $endpoint, ['date' => $date->format('Y-m-d')]);
    }

    /**
     * @param \DateTime $date
     * @param $seats
     * @param $duration
     * @param $smoking
     * @param int $eventId
     * @return mixed
     */
    public function vefifyCapacity(\DateTime $date, $seats, $duration, $smoking, $eventId = 0)
    {
        $params = [
            'date' => $date->format('Y-m-d'),
            'time' => $date->format('H:i'),
            'seats' => (int)$seats,
            'duration' => (int)$duration,
            'smoking_option' => $smoking
        ];

        if ($eventId) {
            $params['event_id'] = $eventId;
        }

        $endpoint = $this->api->generateEndpoint([self::RESOURCE_NAME, $this->id, 'verify_capacity']);
        return $this->api->call('POST', $endpoint, $params);
    }

    /**
     * @param \DateTime $date
     * @param $seats
     * @param $duration
     * @param $name
     * @param $email
     * @param $phone
     * @param $smoking
     * @param string $note
     * @param string $voucherType
     * @param string $voucherCode
     * @param bool $verification
     * @return mixed
     * @throws \Exception
     */
    public function createReservation(
        \DateTime $date,
        $seats,
        $duration,
        $name,
        $email,
        $phone,
        $smoking,
        $note = '',
        $voucherType = '',
        $voucherCode = '',
        $verification = true
    ) {

        $params = [
            'date' => $date->format('Y-m-d'),
            'time' => $date->format('H:i'),
            'seats' => (int)$seats,
            'duration' => (int)$duration,
            'smoking_table' => $smoking,
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone,
            'note' => $note
        ];

        if ($voucherType && $voucherCode) {
            $params['voucher_type'] = $voucherType;
            $params['voucher_code'] = $voucherCode;
        }

        $endpoint = $this->api->generateEndpoint(
            [self::RESOURCE_NAME, $this->id, 'reservations'],
            ['verification' => $verification]
        );
        return $this->api->call('POST', $endpoint, $params);
    }
}
