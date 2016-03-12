<?php

namespace FreezyBee\Restu\DI;

use Nette\DI\CompilerExtension;
use Nette\Utils\AssertionException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

/**
 * Class RestuExtension
 * @package FreezyBee\Restu\DI
 */
class RestuExtension extends CompilerExtension
{
    private $defaults = [
        'apiKey' => null,
        'apiUrl' => 'https://rest-api.restu.cz/',
        'version' => 'v1',
        'debugger' => '%debugMode%'
    ];

    public function loadConfiguration()
    {
        $config = $this->getConfig($this->defaults);

        // validate apiKey
        Validators::assert($config['apiKey'], 'string', 'Restu - missing apiKey');

        // validate apiUrl
        Validators::assert($config['apiUrl'], 'string', 'Restu - invalid apiUrl');

        // validate apiVersion
        Validators::assert($config['apiUrl'], 'string', 'Restu - invalid version');

        Validators::assert($config['apiUrl'], 'url', 'Restu - wrong apiUrl');

        $builder = $this->getContainerBuilder();

        $api = $builder->addDefinition($this->prefix('api'))
            ->setClass('FreezyBee\Restu\Api')
            ->setArguments([$config]);

        if ($config['debugger']) {
            $builder->addDefinition($this->prefix('panel'))
                ->setClass('FreezyBee\Restu\Diagnostics\Panel')
                ->setInject(false);
            $api->addSetup($this->prefix('@panel') . '::register', ['@self']);
        }
    }
}
